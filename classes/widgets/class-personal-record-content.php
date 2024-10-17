<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Query;
use Progress_Planner\Goals\Goal_Recurring;
use Progress_Planner\Goals\Goal;
use Progress_Planner\Widgets\Widget;

/**
 * Personal record content widget.
 */
final class Personal_Record_Content extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'personal-record-content';

	/**
	 * Get the personal record goal.
	 *
	 * @return array
	 */
	public function personal_record_callback() {
		$goal = Goal_Recurring::get_instance(
			'weekly_post_record',
			[
				'class_name'  => Goal::class,
				'id'          => 'weekly_post',
				'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
				'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
				'status'      => 'active',
				'priority'    => 'low',
				'evaluate'    => function ( $goal_object ) {
					return (bool) count(
						Query::get_instance()->query_activities(
							[
								'category'   => 'content',
								'type'       => 'publish',
								'start_date' => $goal_object->get_details()['start_date'],
								'end_date'   => $goal_object->get_details()['end_date'],
							]
						)
					);
				},
			],
			[
				'frequency'     => 'weekly',
				'start'         => new \DateTime( '-2 years' ),
				'end'           => new \DateTime(), // Today.
				'allowed_break' => 0, // Do not allow breaks in the streak.
			]
		);

		return $goal->get_streak();
	}
}
