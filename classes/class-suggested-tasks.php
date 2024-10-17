<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Suggested_Tasks\Scripts;
use Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Posts as Local_Tasks_Update_Posts;
use Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Core as Local_Tasks_Update_Core;
use Progress_Planner\Activities\Suggested_Task as Suggested_Task_Activity;

/**
 * Settings class.
 */
class Suggested_Tasks {

	/**
	 * The name of the settings option.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'progress_planner_suggested_tasks';

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		new Scripts();
		new Local_Tasks_Update_Posts();
		new Local_Tasks_Update_Core();
		$this->maybe_unsnooze_tasks();
	}

	/**
	 * Mark a task as completed.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return void
	 */
	public static function mark_task_as_completed( $task_id ) {
		$activity          = new Suggested_Task_Activity();
		$activity->type    = 'completed';
		$activity->data_id = (string) $task_id;
		$activity->date    = new \DateTime();
		$activity->user_id = \get_current_user_id();
		$activity->save();

		self::mark_task_as_pending_celebration( $task_id );
	}

	/**
	 * Mark a task as pending celebration.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool
	 */
	public static function mark_task_as_pending_celebration( $task_id ) {
		$option              = \get_option( self::OPTION_NAME, [] );
		$pending_celebration = $option['pending_celebration'] ?? [];
		if ( \in_array( $task_id, $pending_celebration, true ) ) {
			return false;
		}
		$pending_celebration[] = (string) $task_id;
		return \update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Mark a task as celebrated.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool
	 */
	public static function mark_task_as_celebrated( $task_id ) {
		$option              = \get_option( self::OPTION_NAME, [] );
		$pending_celebration = $option['pending_celebration'] ?? [];
		if ( ! \in_array( $task_id, $pending_celebration, true ) ) {
			return false;
		}
		unset( $pending_celebration[ \array_search( $task_id, $pending_celebration, true ) ] );
		$option['pending_celebration'] = $pending_celebration;
		return \update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Maybe celebrate tasks.
	 *
	 * @return bool
	 */
	public static function maybe_celebrate_tasks() {
		$option              = \get_option( self::OPTION_NAME, [] );
		$pending_celebration = $option['pending_celebration'] ?? [];

		if ( empty( $pending_celebration ) ) {
			return false;
		}
		$option['pending_celebration'] = [];
		return \update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Mark a task as snoozed.
	 *
	 * @param string $task_id The task ID.
	 * @param string $duration The duration.
	 *
	 * @return bool
	 */
	public static function mark_task_as_snoozed( $task_id, $duration ) {
		$option  = \get_option( self::OPTION_NAME, [] );
		$snoozed = $option['snoozed'] ?? [];

		switch ( $duration ) {
			case '1-month':
				$time = \MONTH_IN_SECONDS;
				break;

			case '3-months':
				$time = 3 * \MONTH_IN_SECONDS;
				break;

			case '6-months':
				$time = 6 * \MONTH_IN_SECONDS;
				break;

			case '1-year':
				$time = \YEAR_IN_SECONDS;
				break;

			case 'forever':
				$time = \PHP_INT_MAX;
				break;

			default:
				$time = \WEEK_IN_SECONDS;
				break;
		}

		// Check if there's already an item with the same ID.
		$item_exists = false;
		foreach ( $snoozed as $key => $snoozed_task ) {
			if ( $snoozed_task['id'] === $task_id ) {
				$snoozed[ $key ]['time'] = \time() + $time;
				$item_exists             = true;
				break;
			}
		}

		if ( ! $item_exists ) {
			$snoozed[] = [
				'id'   => (string) $task_id,
				'time' => \time() + $time,
			];
		}
		$option['snoozed'] = $snoozed;

		return \update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Maybe unsnooze tasks.
	 *
	 * @return void
	 */
	private function maybe_unsnooze_tasks() {
		$option = \get_option( self::OPTION_NAME, [] );
		if ( ! isset( $option['snoozed'] ) ) {
			return;
		}
		$current_time = \time();

		$update = false;
		foreach ( $option['snoozed'] as $key => $task ) {
			if ( $task['time'] < $current_time ) {
				unset( $option['snoozed'][ $key ] );
				$update = true;
			}
		}
		if ( $update ) {
			\update_option( self::OPTION_NAME, $option );
		}
	}
}