<?php
/**
 * Handle user badges.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Query;

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

		return $badge['progress_callback']();
	}

	/**
	 * Register Core badges.
	 *
	 * @return void
	 */
	private function register_badges() {
		// First Post.
		$this->register_badge(
			'first_post',
			[
				'name'              => __( 'First Post', 'progress-planner' ),
				'description'       => __( 'You published your first post.', 'progress-planner' ),
				'progress_callback' => function () {
					$activities = \progress_planner()->get_query()->query_activities(
						[
							'category' => 'content',
							'type'     => 'publish',
						]
					);
					return empty( $activities ) ? 0 : 100;
				},
			]
		);

		// 100 posts.
		$this->register_badge(
			'100_posts',
			[
				'name'              => __( '100 Posts', 'progress-planner' ),
				'description'       => __( 'You published 100 posts.', 'progress-planner' ),
				'progress_callback' => function () {
					$activities = \progress_planner()->get_query()->query_activities(
						[
							'category' => 'content',
							'type'     => 'publish',
						]
					);
					return min( count( $activities ), 100 );
				},
			]
		);

		// 1000 posts
		$this->register_badge(
			'1000_posts',
			[
				'name'              => __( '1000 Posts', 'progress-planner' ),
				'description'       => __( 'You published 1000 posts.', 'progress-planner' ),
				'progress_callback' => function () {
					$activities = \progress_planner()->get_query()->query_activities(
						[
							'category' => 'content',
							'type'     => 'publish',
						]
					);
					return min( floor( count( $activities ) / 10 ), 100 );
				},
			]
		);

		// 2000 posts
		$this->register_badge(
			'2000_posts',
			[
				'name'              => __( '1000 Posts', 'progress-planner' ),
				'description'       => __( 'You published 1000 posts.', 'progress-planner' ),
				'progress_callback' => function () {
					$activities = \progress_planner()->get_query()->query_activities(
						[
							'category' => 'content',
							'type'     => 'publish',
						]
					);
					return min( floor( count( $activities ) / 20 ), 100 );
				},
			]
		);

		// 5000 posts
		$this->register_badge(
			'5000_posts',
			[
				'name'              => __( '5000 Posts', 'progress-planner' ),
				'description'       => __( 'You published 5000 posts.', 'progress-planner' ),
				'progress_callback' => function () {
					$activities = \progress_planner()->get_query()->query_activities(
						[
							'category' => 'content',
							'type'     => 'publish',
						]
					);
					return min( floor( count( $activities ) / 50 ), 100 );
				},
			]
		);
	}
}
