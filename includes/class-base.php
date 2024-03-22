<?php
/**
 * Progress Planner main plugin class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Query;
use ProgressPlanner\Admin\Page as Admin_page;
use ProgressPlanner\Admin\Dashboard_Widget as Admin_Dashboard_Widget;
use ProgressPlanner\Actions\Content as Actions_Content;
use ProgressPlanner\Actions\Maintenance as Actions_Maintenance;
use ProgressPlanner\Settings;
use ProgressPlanner\Tracker;
use ProgressPlanner\Badges\Wonderful_Writer as Badge_Wonderful_Writer;
use ProgressPlanner\Badges\Awesome_Author as Badge_Awesome_Author;
use ProgressPlanner\Badges\Notorious_Novelist as Badge_Notorious_Novelist;
use ProgressPlanner\Badges\Progress_Professional as Badge_Progress_Professional;
use ProgressPlanner\Badges\Maintenance_Maniac as Badge_Maintenance_Maniac;

/**
 * Main plugin class.
 */
class Base {

	/**
	 * An instance of this class.
	 *
	 * @var \ProgressPlanner\Base
	 */
	private static $instance;

	/**
	 * The remote server ROOT URL.
	 *
	 * @var string
	 */
	const REMOTE_SERVER_ROOT_URL = 'https://joost.blog';

	/**
	 * Get the single instance of this class.
	 *
	 * @return \ProgressPlanner\Base
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		new Admin_Page();
		new Admin_Dashboard_Widget();
		new Actions_Content();
		new Actions_Maintenance();

		new Badge_Wonderful_Writer();
		new Badge_Awesome_Author();
		new Badge_Notorious_Novelist();

		new Badge_Progress_Professional();
		new Badge_Maintenance_Maniac();
		require_once \PROGRESS_PLANNER_DIR . '/includes/badges/maintenance-super-site-specialist.php';

		require_once \PROGRESS_PLANNER_DIR . '/includes/badges/streak-content.php';
	}

	/**
	 * Get the query object.
	 *
	 * @return \ProgressPlanner\Query
	 */
	public function get_query() {
		return Query::get_instance();
	}

	/**
	 * Get the activation date.
	 *
	 * @return \DateTime
	 */
	public static function get_activation_date() {
		$activation_date = Settings::get( 'activation_date' );
		if ( ! $activation_date ) {
			$activation_date = new \DateTime();
			Settings::set( 'activation_date', $activation_date->format( 'Y-m-d' ) );
			return $activation_date;
		}
		return \DateTime::createFromFormat( 'Y-m-d', $activation_date );
	}

	/**
	 * Get the feed from the blog.
	 *
	 * @return array
	 */
	public function get_blog_feed() {
		$feed = \get_transient( 'prpl_blog_feed' );
		if ( false === $feed ) {
			// Get the feed using the REST API.
			$response = \wp_remote_get( self::REMOTE_SERVER_ROOT_URL . '/wp-json/wp/v2/posts/?per_page=3' );
			if ( \is_wp_error( $response ) ) {
				return [];
			}
			$feed = json_decode( \wp_remote_retrieve_body( $response ), true );
			\set_transient( 'prpl_blog_feed', $feed, 1 * DAY_IN_SECONDS );
		}
		return $feed;
	}

	/**
	 * THIS SHOULD BE DELETED.
	 * WE ONLY HAVE IT HERE TO EXPERIMENT WITH THE NUMBERS
	 * WE'LL HAVE TO USE FOR STATS/SCORES.
	 *
	 * TODO: DELETE THIS METHOD.
	 *
	 * @param string $param The parameter to get. Null to get all.
	 *
	 * @return mixed
	 */
	public function get_dev_config( $param = null ) {
		$config = [
			'content-publish'                    => 50,
			'content-update'                     => 10,
			'content-delete'                     => 5,
			'content-100-minus-words-multiplier' => 0.8,
			'content-100-plus-words-multiplier'  => 1.1,
			'content-350-plus-words-multiplier'  => 1.25,
			'content-1000-plus-words-multiplier' => 0.8,
			'maintenance'                        => 10,
			'activity-score-target'              => 200,
		];

		// phpcs:disable WordPress.Security
		foreach ( $config as $key => $value ) {
			$config[ $key ] = isset( $_GET[ $key ] ) ? (float) $_GET[ $key ] : $value;
		}
		// phpcs:enable WordPress.Security

		return null === $param ? $config : $config[ $param ];
	}

	/**
	 * Init the tracker.
	 *
	 * @return void
	 */
	public function init_tracker() {
		$data_callback = function () {
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
				\progress_planner()->get_query()->query_activities(
					[
						'start_date' => new \DateTime( '-7 days' ),
					]
				)
			);

			// Get the badges.
			$data['badges'] = Settings::get( 'badges' );

			return $data;
		};

		new Tracker(
			[
				'namespace'     => 'progress-planner',
				'interval'      => WEEK_IN_SECONDS,
				'remote-server' => 'https://progressplanner.com/',
				'collect-data'  => $data_callback,
			]
		);
	}
}
