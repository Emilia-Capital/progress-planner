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
		$occurences = array_reverse( $goal->get_occurences() );
		$streak_nr  = 0;

		foreach ( $occurences as $occurence ) {
			// If the goal was not met, break the streak.
			if ( ! $occurence->evaluate() ) {
				break;
			}

			++$streak_nr;
		}

		return [
			'number'      => $streak_nr,
			'color'       => 'hsl(' . (int) min( 100, $streak_nr * 100 / $target ) . ', 100%, 40%)',
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
					'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
					'status'      => 'active',
					'priority'    => 'low',
					'evaluate'    => function ( $goal_object ) use ( $stats ) {
						return (bool) count(
							$stats->get_stats(
								$goal_object->get_details()['start_date'],
								$goal_object->get_details()['end_date'],
								[]
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

	/**
	 * Register a weekly-words goal.
	 *
	 * @return void
	 */
	private function get_weekly_words_goal() {
		$stats = new Stat_Posts();

		$stats_value = $stats->get_value();

		// Bail early if there are no stats.
		if ( empty( $stats_value ) ) {
			return;
		}

		return new Goal_Recurring(
			new Goal_Posts(
				[
					'id'          => 'weekly_words',
					'title'       => \esc_html__( 'Write 500 words/week', 'progress-planner' ),
					'description' => \esc_html__( 'Streak: The number of weeks this goal has been accomplished consistently.', 'progress-planner' ),
					'status'      => 'active',
					'priority'    => 'low',
					'evaluate'    => function ( $goal_object ) use ( $stats ) {
						$words = 0;
						$posts = $stats->get_stats(
							$goal_object->get_details()['start_date'],
							$goal_object->get_details()['end_date'],
							[ 'post' ]
						);
						foreach ( $posts as $post_dates ) {
							foreach ( $post_dates as $post_details ) {
								$words += $post_details['words'];
							}
						}
						return $words >= 500;
					},
				]
			),
			'weekly',
			array_keys( $stats_value )[0], // Beginning of the stats.
			gmdate( Date::FORMAT ) // Today.
		);
	}
}
