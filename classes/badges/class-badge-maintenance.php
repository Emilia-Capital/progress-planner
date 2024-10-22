<?php
/**
 * Badge object.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Badges;

use Progress_Planner\Base;
use Progress_Planner\Goals\Goal_Recurring;
use Progress_Planner\Goals\Goal;

/**
 * Badge class.
 */
abstract class Badge_Maintenance extends Badge {

	/**
	 * Get a recurring goal for any type of weekly activity.
	 *
	 * @return Goal_Recurring
	 */
	public function get_goal() {
		global $progress_planner;
		return Goal_Recurring::get_instance(
			'weekly_activity',
			[
				'class_name'  => Goal::class,
				'id'          => 'weekly_activity',
				'title'       => \esc_html__( 'Weekly activity', 'progress-planner' ),
				'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
				'status'      => 'active',
				'priority'    => 'low',
				'evaluate'    => function ( $goal_object ) use ( $progress_planner ) {
					return (bool) count(
						$progress_planner->query->query_activities(
							[
								'start_date' => $goal_object->get_details()['start_date'],
								'end_date'   => $goal_object->get_details()['end_date'],
							]
						)
					);
				},
			],
			[
				'frequency'     => 'weekly',
				'start'         => $progress_planner->get_activation_date(),
				'end'           => new \DateTime(), // Today.
				'allowed_break' => 1, // Allow break in the streak for 1 week.
			]
		);
	}

	/**
	 * Get the saved progress.
	 *
	 * @return array
	 */
	protected function get_saved() {
		$value = parent::get_saved();

		if ( isset( $value['progress'] ) && 100 === $value['progress'] ) {
			return $value;
		}

		if ( isset( $value['date'] ) ) {
			$last_date = new \DateTime( $value['date'] );
			$diff      = $last_date->diff( new \DateTime() );
			if ( $diff->days <= 2 ) {
				return $value;
			}
		}

		return [];
	}
}
