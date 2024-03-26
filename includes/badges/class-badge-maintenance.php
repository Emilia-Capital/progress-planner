<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges;

use ProgressPlanner\Base;
use ProgressPlanner\Goals\Goal_Recurring;
use ProgressPlanner\Goals\Goal;

/**
 * Badge class.
 */
abstract class Badge_Maintenance extends Badge {

	/**
	 * The badge category.
	 *
	 * @var string
	 */
	protected $category = 'streak_any_task';

	/**
	 * Get a recurring goal for any type of weekly activity.
	 *
	 * @return Goal_Recurring
	 */
	public function get_goal() {
		return Goal_Recurring::get_instance(
			'weekly_activity',
			[
				'class_name'  => Goal::class,
				'id'          => 'weekly_activity',
				'title'       => \esc_html__( 'Weekly activity', 'progress-planner' ),
				'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
				'status'      => 'active',
				'priority'    => 'low',
				'evaluate'    => function ( $goal_object ) {
					return (bool) count(
						\progress_planner()->get_query()->query_activities(
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
				'start'         => Base::get_activation_date(),
				'end'           => new \DateTime(), // Today.
				'allowed_break' => 1, // Allow break in the streak for 1 week.
			]
		);
	}
}
