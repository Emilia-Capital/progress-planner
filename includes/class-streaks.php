<?php
/**
 * Handle streaks.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Goals\Goal_Posts;
use ProgressPlanner\Goals\Goal_Recurring;

/**
 * Streaks class.
 */
class Streaks {

	/**
	 * An array of recurring goals.
	 *
	 * @var Goal_Recurring[]
	 */
	private $recurring_goals = [];

	/**
	 * An instance of this class.
	 *
	 * @var \ProgressPlanner\Streaks
	 */
	private static $instance;

	/**
	 * Get the single instance of this class.
	 *
	 * @return \ProgressPlanner\Streaks
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->register_recurring_goals();
	}

	/**
	 * Register recurring goals.
	 *
	 * @return void
	 */
	private function register_recurring_goals() {
		$this->recurring_goals['weekly_post']  = $this->get_weekly_post_goal();
		$this->recurring_goals['weekly_words'] = $this->get_weekly_words_goal();
	}

	/**
	 * Get the streak for weekly posts.
	 *
	 * @param string $goal_id The goal ID.
	 * @param int    $target  The target number of weeks.
	 *                        Affects the color of the streak.
	 *
	 * @return int The number of weeks for this streak.
	 */
	public function get_streak( $goal_id, $target ) {
		$goal = $this->recurring_goals[ $goal_id ];

		// Bail early if there is no goal.
		if ( ! $goal ) {
			return [
				'number'      => 0,
				'color'       => 'hsl(0, 100%, 40%)',
				'title'       => $goal->get_goal()->get_details()['title'],
				'description' => $goal->get_goal()->get_details()['description'],
			];
		}

		// Reverse the order of the occurences.
		$occurences = $goal->get_occurences();

		// Calculate the streak number.
		$streak_nr = 0;
		foreach ( $occurences as $occurence ) {
			/**
			 * Evaluate the occurence.
			 * If the occurence is true, then increment the streak number.
			 * Otherwise, reset the streak number.
			 */
			$streak_nr = $occurence->evaluate() ? $streak_nr + 1 : 0;
		}

		// Calculate the hue for the color.
		$hue = (int) min( 125, $streak_nr * 125 / $target );

		return [
			'number'      => $streak_nr,
			'color'       => "hsl($hue, 100%, 40%)",
			'title'       => $goal->get_goal()->get_details()['title'],
			'description' => $goal->get_goal()->get_details()['description'],
		];
	}

	/**
	 * Register weekly-post goal.
	 *
	 * @return void
	 */
	private function get_weekly_post_goal() {
		return new Goal_Recurring(
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
									'category'   => 'content',
									'type'       => 'publish',
									'start_date' => $goal_object->get_details()['start_date'],
									'end_date'   => $goal_object->get_details()['end_date'],
									'data'       => [
										'post_type' => 'post',
									],
								]
							)
						);
					},
				]
			),
			'weekly',
			\progress_planner()->get_query()->get_oldest_activity()->get_date(), // Beginning of the stats.
			new \DateTime( 'now' ) // Today.
		);
	}

	/**
	 * Register a weekly-words goal.
	 *
	 * @return void
	 */
	private function get_weekly_words_goal() {
		return new Goal_Recurring(
			new Goal_Posts(
				[
					'id'          => 'weekly_post',
					'title'       => \esc_html__( 'Write a weekly blog post', 'progress-planner' ),
					'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
					'status'      => 'active',
					'priority'    => 'low',
					'evaluate'    => function ( $goal_object ) {
						$activities = \progress_planner()->get_query()->query_activities(
							[
								'category'   => 'content',
								'type'       => 'publish',
								'start_date' => $goal_object->get_details()['start_date'],
								'end_date'   => $goal_object->get_details()['end_date'],
								'data'       => [
									'post_type' => 'post',
								],
							]
						);
						$words      = 0;
						foreach ( $activities as $activity ) {
							$words += $activity->get_data( 'word_count' );
						}
						return $words >= 500;
					},
				]
			),
			'weekly',
			\progress_planner()->get_query()->get_oldest_activity()->get_date(), // Beginning of the stats.
			new \DateTime( 'now' ) // Today.
		);
	}
}
