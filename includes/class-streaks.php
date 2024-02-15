<?php
/**
 * Handle streaks.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Goals\Goal_Posts;
use ProgressPlanner\Goals\Goal_Recurring;
use ProgressPlanner\Stats\Stat_Posts;

/**
 * Streaks class.
 */
class Streaks {

	/**
	 * Get the streak for weekly posts.
	 *
	 * @return int The number of weeks for this streak.
	 */
	public function get_weekly_post_streak() {
		$goal = $this->get_weekly_post_goal();

		// Bail early if there is no goal.
		if ( ! $goal ) {
			return 0;
		}

		// Reverse the order of the occurences.
		$occurences = array_reverse( $goal->get_occurences() );
		$streak_nr  = 0;

		foreach ( $occurences as $occurence ) {
			// If the goal was not met, break the streak.
			if ( ! $occurence->evaluate() ) {
				break;
			}

			++$streak_nr;
		}

		return $streak_nr;
	}

	/**
	 * Register weekly-post goal.
	 *
	 * @return void
	 */
	private function get_weekly_post_goal() {
		$stats = new Stat_Posts();

		$stats_value = $stats->get_value();

		// Bail early if there are no stats.
		if ( empty( $stats_value ) ) {
			return;
		}

		return new Goal_Recurring(
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
			array_keys( $stats_value )[0], // Beginning of the stats.
			gmdate( Date::FORMAT ) // Today.
		);
	}
}
