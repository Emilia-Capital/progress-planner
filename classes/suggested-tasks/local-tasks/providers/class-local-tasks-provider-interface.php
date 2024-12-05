<?php
/**
 * Interface for local tasks.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks\Providers;

/**
 * Local_Tasks interface.
 */
interface Local_Tasks_Provider_Interface {

	/**
	 * Get tasks to inject.
	 *
	 * @return array
	 */
	public function get_tasks_to_inject();

	/**
	 * Evaluate a task.
	 *
	 * @param string $task The task ID.
	 *
	 * @return bool
	 */
	public function evaluate_task( $task );

	/**
	 * Get the task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_task_details( $task_id );

	/**
	 * Get the task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_data_from_task_id( $task_id );

	/**
	 * Get the provider ID.
	 *
	 * @return string
	 */
	public function get_provider_type();
}
