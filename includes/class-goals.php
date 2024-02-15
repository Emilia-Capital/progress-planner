<?php
/**
 * Goals class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Date;
use ProgressPlanner\Stats\Stat_Posts;
use ProgressPlanner\Goals\Goal_Recurring;
use ProgressPlanner\Goals\Goal_Posts;

/**
 * Goals class.
 */
class Goals {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_core_goals();
	}

	/**
	 * Register the goals.
	 *
	 * @return void
	 */
	private function register_core_goals() {
		$this->register_weekly_post_goal();
	}

	/**
	 * Register weekly-post goal.
	 *
	 * @return void
	 */
	private function register_weekly_post_goal() {
		$stats = new Stat_Posts();

		new Goal_Recurring(
			new Goal_Posts(
				[
					'id'          => 'weekly_post',
					'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
					'description' => '',
					'status'      => 'active',
					'priority'    => 'high',
					'evaluate'    => function ( $goal_object ) use ( $stats ) {
						return (bool) count(
							$stats->get_stats(
								$goal_object->get_details()['start_date'],
								$goal_object->get_details()['end_date'],
								[ 'post' ]
							)
						);
					},
				]
			),
			'weekly',
			array_keys( $stats->get_value() )[0], // Beginning of the stats.
			gmdate( Date::FORMAT ) // Today.
		);
	}
}
