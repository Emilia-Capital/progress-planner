<?php
/**
 * A recurring goal to write content weekly.
 *
 * This does not award a badge, but is used to create the personal record badge/widget.
 *
 * @package ProgressPlanner
 */

use ProgressPlanner\Goals\Goal_Recurring;
use ProgressPlanner\Goals\Goal_Posts;
use ProgressPlanner\Settings;
use ProgressPlanner\Badges;

Badges::register_badge(
	'personal_record_content',
	[
		'progress_callback' => function () {
			$goal = Goal_Recurring::get_instance(
				'weekly_post_record',
				[
					'class_name'  => Goal_Posts::class,
					'id'          => 'weekly_post',
					'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
					'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
					'status'      => 'active',
					'priority'    => 'low',
					'evaluate'    => function ( $goal_object ) {
						return (bool) count(
							\progress_planner()->get_query()->query_activities(
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

			$saved_progress = Settings::get( [ 'badges', 'personal_record_content' ], false );
			// If the date is set and shorter than 2 days, return it without querying.
			if ( $saved_progress && is_array( $saved_progress['progress'] ) && ( new \DateTime() )->diff( new \DateTime( $saved_progress['date'] ) )->days < 2 ) {
				return $saved_progress['progress'];
			}

			$final = $goal->get_streak();
			Settings::set(
				[ 'badges', 'personal_record_content' ],
				[
					'progress' => $final,
					'date'     => ( new \DateTime() )->format( 'Y-m-d H:i:s' ),
				]
			);

			return $goal->get_streak();
		},
	]
);
