<?php
/**
 * Goals class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Goals class.
 */
class Goals extends Base {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_core_goals();
	}

	/**
	 * Register the goals.
	 */
	private function register_core_goals() {
		$this->register_weekly_post_goal();
	}

	/**
	 * Register weekly-post goal.
	 */
	private function register_weekly_post_goal() {
		$stats = $this->get_stats();

		// Get the start date for all stats.
		$start_date = array_keys( $this->get_stats()->get_stat( 'posts' )->get_value() );
		sort( $start_date );
		$start_date = $start_date[0];

		new \ProgressPlanner\Goals\Goal_Recurring(
			new \ProgressPlanner\Goals\Goal_Posts(
				[
					'id'          => 'weekly_post',
					'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
					'description' => '',
					'status'      => 'active',
					'priority'    => 'high',
					'evaluate'    => function ( $goal_object ) use ( $stats ) {
						return (bool) count(
							$stats->get_stat( 'posts' )->get_stats(
								$goal_object->get_details()['start_date'],
								$goal_object->get_details()['end_date'],
								[ 'post' ]
							)
						);
					},
				]
			),
			'weekly',
			$start_date, // Beginning of the stats.
			gmdate( 'Ymd' ) // Today.
		);
	}
}
