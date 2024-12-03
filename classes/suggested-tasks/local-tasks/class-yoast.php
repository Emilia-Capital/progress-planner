<?php
/**
 * Add tasks for Yoast integration.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks;

/**
 * Add tasks for Yoast integration.
 */
class Yoast implements \Progress_Planner\Suggested_Tasks\Local_Tasks_Interface {

	/**
	 * Evaluate a task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool|string
	 */
	public function evaluate_task( $task_id ) {
		if ( 0 === strpos( $task_id, 'yoast' ) && class_exists( '\WPSEO_Options' ) && '' !== \WPSEO_Options::get( 'company_logo' ) ) {
			return $task_id;
		}
		return false;
	}

	/**
	 * Get an array of tasks to inject.
	 *
	 * @return array
	 */
	public function get_tasks_to_inject() {
		return true !== $this->is_task_type_snoozed() ? $this->get_tasks() : [];
	}

	/**
	 * Get the tasks to set Yoast organization logo.
	 *
	 * @return array
	 */
	public function get_tasks() {
		// If all updates are performed, do not add the task.
		if ( class_exists( '\WPSEO_Options' ) && '' !== \WPSEO_Options::get( 'company_logo' ) ) {
			return [];
		}

		return [
			$this->get_task_details( 'yoast-' . \gmdate( 'YW' ) ),
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
			'title'       => \esc_html__( 'Yoast: Set organization logo', 'progress-planner' ),
			'parent'      => 0,
			'priority'    => 'high',
			'type'        => 'maintenance',
			'points'      => 1,
			'description' => '<p>' . \esc_html__( 'Set organization logo to make your website look more professional.', 'progress-planner' ) . '</p>',
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
			'type' => 'yoast',
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
			if ( 'yoast' === $task['id'] ) {
				return true;
			}
		}

		return false;
	}
}
