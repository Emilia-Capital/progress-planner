<?php
/**
 * Abstract class for local tasks.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

/**
 * Local_Tasks class.
 */
abstract class Local_Tasks {

	/**
	 * Get tasks to inject.
	 *
	 * @return array
	 */
	abstract protected function get_tasks_to_inject();

	/**
	 * Evaluate a task.
	 *
	 * @param string $task The task ID.
	 *
	 * @return bool
	 */
	abstract protected function evaluate_task( $task );

	/**
	 * Get the task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	abstract public function get_task_details( $task_id );

	/**
	 * Get the task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	abstract public function get_data_from_task_id( $task_id );
}
