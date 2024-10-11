<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

use Progress_Planner\Suggested_Tasks;

use Progress_Planner\Suggested_Tasks\API as Suggested_Tasks_API;

/**
 * Settings class.
 */
class Evaluation {

	/**
	 * The API object.
	 *
	 * @var Suggested_Tasks_API
	 */
	private $api;

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
		$this->api = new Suggested_Tasks_API();
		$this->register_hooks();
	}

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		\add_action( 'progress_planner_activity_saved', [ $this, 'activity_saved' ] );
	}

	/**
	 * Handle the activity saved event.
	 *
	 * @param \Progress_Planner\Activity $activity The activity.
	 *
	 * @return void
	 */
	public function activity_saved( $activity ) {
		if ( 'suggested_task' === $activity->category ) {
			return;
		}

		// Get tasks and filter only tasks where `completion_type` is set to `auto`.
		$tasks = \array_filter(
			(array) $this->api->get_tasks(),
			function ( $task ) {
				return isset( $task['completion_type'] ) && 'auto' === $task['completion_type'];
			}
		);

		foreach ( $tasks as $task ) {
			$this->evaluate_task_conditions( $task, $activity );
		}
	}

	/**
	 * Evaluate the task conditions.
	 *
	 * @param array                      $task  The task.
	 * @param \Progress_Planner\Activity $activity The activity.
	 *
	 * @return void
	 */
	private function evaluate_task_conditions( $task, $activity ) {
		if (
			is_array( $task )
			&& isset( $task['callback'] )
			&& is_callable( $task['callback'] )
			&& $task['callback']( $activity )
		) {
			Suggested_Tasks::mark_task_as_completed( $task['task_id'] );
			return;
		}
	}
}
