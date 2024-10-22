<?php
/**
 * Progress_Planner REST-API.
 *
 * Adds a REST-API endpoint to get stats, in a URL like:
 * <site-url>/wp-json/progress-planner/v1/get-stats/token/<site-token>
 *
 * The token is generated and saved in the settings.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Widgets\Activity_Scores;

/**
 * Rest_API class.
 */
class Rest_API {
	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'rest_api_init', [ $this, 'register_rest_endpoint' ] );
	}

	/**
	 * Register the REST-API endpoint.
	 *
	 * @return void
	 */
	public function register_rest_endpoint() {
		\register_rest_route(
			'progress-planner/v1',
			'/get-stats/(?P<token>\S+)',
			[
				[
					'methods'             => 'GET',
					'callback'            => [ $this, 'get_stats' ],
					'permission_callback' => '__return_true',
					'args'                => [
						'token' => [
							'required'          => true,
							'validate_callback' => [ $this, 'validate_token' ],
						],
					],
				],
			]
		);
	}

	/**
	 * Receive the data from the client.
	 *
	 * This method handles a REST request and returns a REST response.
	 *
	 * @param \WP_REST_Request $request The REST request object.
	 *
	 * @return \WP_REST_Response The REST response object containing the stats.
	 */
	public function get_stats( \WP_REST_Request $request ) {
		global $progress_planner;
		$data = $request->get_json_params();

		$data = [];

		// Get the number of pending updates.
		$data['pending_updates'] = \wp_get_update_data()['counts']['total'];

		// Get number of content from any public post-type, published in the past week.
		$data['weekly_posts'] = count(
			\get_posts(
				[
					'post_status'    => 'publish',
					'post_type'      => 'post',
					'date_query'     => [ [ 'after' => '1 week ago' ] ],
					'posts_per_page' => 10,
				]
			)
		);

		// Get the number of activities in the past week.
		$data['activities'] = count(
			$progress_planner->get_query()->query_activities(
				[
					'start_date' => new \DateTime( '-7 days' ),
				]
			)
		);

		// Get the website activity score.
		$activity_score           = new Activity_Scores();
		$data['website_activity'] = [
			'score'     => $activity_score->get_score(),
			'checklist' => $activity_score->get_checklist_results(),
		];

		// Get the badges.
		$badges = array_merge(
			$progress_planner->get_badges()->get_badges( 'content' ),
			$progress_planner->get_badges()->get_badges( 'maintenance' ),
			$progress_planner->get_badges()->get_badges( 'monthly' )
		);

		$data['badges'] = [];
		foreach ( $badges as $badge ) {
			$data['badges'][ $badge->get_id() ] = array_merge(
				[
					'id'   => $badge->get_id(),
					'name' => $badge->get_name(),
				],
				$badge->progress_callback()
			);
		}

		$data['latest_badge'] = $progress_planner->get_badges()->get_latest_completed_badge();

		$scores = $progress_planner->get_chart()->get_chart_data(
			[
				'query_params'   => [],
				'dates_params'   => [
					'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( '-6 months' ),
					'end'       => new \DateTime(),
					'frequency' => 'monthly',
					'format'    => 'M',
				],
				'count_callback' => function ( $activities, $date ) {
					$score = 0;
					foreach ( $activities as $activity ) {
						$score += $activity->get_points( $date );
					}
					$target = Base::$points_config['score-target'];
					return $score * 100 / $target;
				},
				'compound'       => false,
				'normalized'     => true,
				'max'            => 100,
			]
		);
		unset( $scores['datasets'][0]['data']['tension'] );
		$data['scores'] = [];
		foreach ( $scores['labels'] as $key => $label ) {
			$data['scores'][] = [
				'label' => $label,
				'value' => $scores['datasets'][0]['data'][ $key ],
			];
		}

		// The website URL.
		$data['website'] = \home_url();

		// Timezone offset.
		$data['timezone_offset'] = \wp_timezone()->getOffset( new \DateTime( 'midnight' ) ) / 3600;

		// Pending todo items.
		$todo_items         = $progress_planner->get_todo()->get_items();
		$pending_todo_items = [];
		foreach ( $todo_items as $item ) {
			if ( ! $todo_items['done'] ) {
				$pending_todo_items[] = $item['content'];
			}
		}
		$data['todo'] = $pending_todo_items;

		$data['plugin_url'] = \esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) );

		$data = \apply_filters( 'progress_planner_rest_api_get_stats', $data );

		return new \WP_REST_Response( $data );
	}

	/**
	 * Validate the token.
	 *
	 * @param string $token The token.
	 *
	 * @return bool
	 */
	public function validate_token( $token ) {
		$token       = str_replace( 'token/', '', $token );
		$license_key = \get_option( 'progress_planner_license_key', false );

		return $license_key && $token === $license_key;
	}
}
