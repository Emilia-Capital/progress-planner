<?php
/**
 * Handle Suggestred-tasks items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks;

use Progress_Planner\Suggested_Tasks\Local_Tasks;
use Progress_Planner\Suggested_Tasks;

/**
 * Handle Suggestred-tasks items.
 */
class Update_Core extends Local_Tasks {

	/**
	 * Evaluate a task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool
	 */
	public function evaluate_task( $task_id ) {
		if ( 'update-core' === $task_id && 0 === \wp_get_update_data()['counts']['total'] ) {
			Suggested_Tasks::mark_task_as_completed( $task_id . '-' . \gmdate( 'YW' ) );
			return true;
		}
		return false;
	}

	/**
	 * Get an array of tasks to inject.
	 *
	 * @return array
	 */
	public function get_tasks_to_inject() {
		// If all updates are performed, do not add the task.
		if ( 0 === \wp_get_update_data()['counts']['total'] ) {
			return [];
		}

		return [
			[
				'task_id'     => 'update-core',
				'title'       => \esc_html__( 'Perform all updates', 'progress-planner' ),
				'parent'      => 0,
				'priority'    => 'high',
				'type'        => 'maintenance',
				'description' => '<p>' . \esc_html__( 'Perform all updates to ensure your website is secure and up-to-date.', 'progress-planner' ) . '</p>',
			],
		];
	}
}
