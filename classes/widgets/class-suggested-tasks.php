<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Query;

/**
 * Published Content Widget.
 */
final class Suggested_Tasks extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'suggested-tasks';

	/**
	 * Get the score.
	 *
	 * @return int The score.
	 */
	public static function get_score() {
		$activities = Query::get_instance()->query_activities(
			[
				'category'   => 'suggested_task',
				// Use 31 days to take into account
				// the activities score decay from previous activities.
				'start_date' => new \DateTime( '-31 days' ),
			]
		);

		/*
		If we need to get the pending activities count, we can use the following code:

		$pending_activities = \get_option( \Progress_Planner\Suggested_Tasks::OPTION_NAME, [] );
		$pending_activities_count = count( $pending_activities );
		$total_count = $activities_count + $pending_activities_count;
		 */

		$score = 0;

		foreach ( $activities as $activity ) {
			$score += $activity->get_points( $activity->date );
		}

		// We need 7 points to reach the monthly goal.
		$score = $score * 100 / 7;

		return (int) min( 100, max( 0, floor( $score ) ) );
	}
}
