<?php
/**
 * Add tasks for Core updates.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks;

/**
 * Add tasks for Core updates.
 */
class Update_Core extends \Progress_Planner\Suggested_Tasks\Local_Tasks {

	/**
	 * Evaluate a task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool|string
	 */
	public function evaluate_task( $task_id ) {
		if ( 'update-core' === $task_id && 0 === \wp_get_update_data()['counts']['total'] ) {
			return $task_id . '-' . \gmdate( 'YW' );
		}
		return false;
	}

	/**
	 * Get an array of tasks to inject.
	 *
	 * @return array
	 */
	public function get_tasks_to_inject() {
		return true !== $this->is_task_type_snoozed() ? $this->get_tasks_to_update_core() : [];
	}

	/**
	 * Get the tasks to update core.
	 *
	 * @return array
	 */
	public function get_tasks_to_update_core() {
		// If all updates are performed, do not add the task.
		if ( 0 === \wp_get_update_data()['counts']['total'] ) {
			return [];
		}

		return [
			$this->get_task_details( 'update-core' ),
		];
	}

	/**
	 * Get the task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_task_details( $task_id ) {

		return [
			'task_id'     => $task_id,
			'title'       => \esc_html__( 'Perform all updates', 'progress-planner' ),
			'parent'      => 0,
			'priority'    => 'high',
			'type'        => 'maintenance',
			'points'      => 1,
			'description' => '<p>' . \esc_html__( 'Perform all updates to ensure your website is secure and up-to-date.', 'progress-planner' ) . '</p>',
		];
	}

	/**
	 * Get the data from a task-ID.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array The data.
	 */
	public function get_data_from_task_id( $task_id ) {
		$data = [
			'type' => 'update-core',
			'id'   => $task_id,
		];

		return $data;
	}

	/**
	 * Check if a task type is snoozed.
	 *
	 * @return bool
	 */
	public function is_task_type_snoozed() {
		$snoozed = \progress_planner()->get_suggested_tasks()->get_snoozed_tasks();
		if ( ! \is_array( $snoozed ) || empty( $snoozed ) ) {
			return false;
		}

		foreach ( $snoozed as $task ) {
			if ( 'update-core' === $task['id'] ) {
				return true;
			}
		}

		return false;
	}
}
