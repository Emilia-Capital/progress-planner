<?php
/**
 * Handle user badges.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Goals\Goal_Recurring;
use ProgressPlanner\Goals\Goal_Posts;

/**
 * Badges class.
 */
class Badges {

	/**
	 * The name of the badges option.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'progress_planner_badges';

	/**
	 * Registered badges.
	 *
	 * @var array
	 */
	private $badges = [];

	/**
	 * Badges progress.
	 *
	 * @var array
	 */
	private $badges_progress = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->badges_progress = \get_option( self::OPTION_NAME, [] );
		$this->register_badges();
	}

	/**
	 * Register a badge.
	 *
	 * @param string $badge_id The badge ID.
	 * @param array  $args     The badge arguments.
	 *
	 * @return void
	 */
	public function register_badge( $badge_id, $args ) {
		$args['id']                = $badge_id;
		$this->badges[ $badge_id ] = $args;
	}

	/**
	 * Get a badge by ID.
	 *
	 * @param string $badge_id The badge ID.
	 *
	 * @return array
	 */
	public function get_badge( $badge_id ) {
		return isset( $this->badges[ $badge_id ] ) ? $this->badges[ $badge_id ] : [];
	}

	/**
	 * Get all badges.
	 *
	 * @return array
	 */
	public function get_badges() {
		return $this->badges;
	}

	/**
	 * Get the progress for a badge.
	 *
	 * @param string $badge_id The badge ID.
	 *
	 * @return int
	 */
	public function get_badge_progress( $badge_id ) {
		$badge = $this->get_badge( $badge_id );
		if ( empty( $badge ) ) {
			return 0;
		}

		$progress = [];

		foreach ( $badge['steps'] as $step ) {
			$progress[] = [
				'name'     => $step['name'],
				'icon'     => $step['icon'],
				'progress' => $badge['progress_callback']( $step['target'] ),
			];
		}

		return $progress;
	}

	/**
	 * Register Core badges.
	 *
	 * @return void
	 */
	private function register_badges() {
		// Badges for number of posts.
		$this->register_badge(
			'content_published_count',
			[
				'steps'             => [
					[
						'target' => 100,
						'name'   => __( '100 Posts', 'progress-planner' ),
						'icon'   => '🏆',
					],
					[
						'target' => 1000,
						'name'   => __( '1000 Posts', 'progress-planner' ),
						'icon'   => '🏆',
					],
					[
						'target' => 2000,
						'name'   => __( '2000 Posts', 'progress-planner' ),
						'icon'   => '🏆',
					],
					[
						'target' => 5000,
						'name'   => __( '5000 Posts', 'progress-planner' ),
						'icon'   => '🏆',
					],
				],
				'progress_callback' => function ( $target ) {
					$activities = \progress_planner()->get_query()->query_activities(
						[
							'category' => 'content',
							'type'     => 'publish',
						]
					);
					return min( floor( 100 * count( $activities ) / $target ), 100 );
				},
			]
		);

		// 100 maintenance tasks.
		$this->register_badge(
			'maintenance_tasks',
			[
				'steps'             => [
					[
						'target' => 10,
						'name'   => __( '10 maintenance tasks', 'progress-planner' ),
						'icon'   => '🏆',
					],
					[
						'target' => 100,
						'name'   => __( '100 maintenance tasks', 'progress-planner' ),
						'icon'   => '🏆',
					],
					[
						'target' => 1000,
						'name'   => __( '1000 maintenance tasks', 'progress-planner' ),
						'icon'   => '🏆',
					],
				],
				'progress_callback' => function ( $target ) {
					$activities = \progress_planner()->get_query()->query_activities(
						[
							'category' => 'maintenance',
						]
					);
					return min( floor( 100 * count( $activities ) / $target ), 100 );
				},
			]
		);

		// Write a post for 10 consecutive weeks.
		$this->register_badge(
			'consecutive_weeks_posts',
			[
				'steps'             => [
					[
						'target' => 10,
						'name'   => __( '10 weeks posting streak', 'progress-planner' ),
						'icon'   => '🏆',
					],
					[
						'target' => 52,
						'name'   => __( '52 weeks posting streak', 'progress-planner' ),
						'icon'   => '🏆',
					],
					[
						'target' => 104,
						'name'   => __( '104 weeks posting streak', 'progress-planner' ),
						'icon'   => '🏆',
					],
					[
						'target' => 208,
						'name'   => __( '208 weeks posting streak', 'progress-planner' ),
						'icon'   => '🏆',
					],
				],
				'progress_callback' => function ( $target ) {
					$goal = new Goal_Recurring(
						new Goal_Posts(
							[
								'id'          => 'weekly_post',
								'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
								'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
								'status'      => 'active',
								'priority'    => 'low',
								'evaluate'    => function ( $goal_object ) {
									return (bool) count(
										\progress_planner()->get_query()->query_activities(
											[
												'category' => 'content',
												'type'     => 'publish',
												'start_date' => $goal_object->get_details()['start_date'],
												'end_date' => $goal_object->get_details()['end_date'],
											]
										)
									);
								},
							]
						),
						'weekly',
						\progress_planner()->get_query()->get_oldest_activity()->get_date(), // Beginning of the stats.
						new \DateTime() // Today.
					);

					return min( floor( 100 * $goal->get_streak()['max_streak'] / $target ), 100 );
				},
			]
		);
	}
}
