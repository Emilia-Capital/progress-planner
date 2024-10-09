<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks;

use Progress_Planner\Suggested_Tasks\Local_Tasks;
use Progress_Planner\Suggested_Tasks;

/**
 * Settings class.
 */
class Update_Core extends Local_Tasks {

	/**
	 * Evaluate a task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return void
	 */
	public function evaluate_task( $task_id ) {
		if ( 'update-core' !== $task_id ) {
			return;
		}
		if ( 0 === \wp_get_update_data()['counts']['total'] ) {
			Suggested_Tasks::mark_task_as_completed( $task_id . '-' . \gmdate( 'Y-m-d' ) );
			self::remove_pending_task( $task_id );
		}
	}

	/**
	 * Filter the tasks.
	 *
	 * @param array $tasks The tasks.
	 *
	 * @return array
	 */
	public function inject_tasks( $tasks ) {
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}
		// If all updates are performed, do not add the task.
		if ( 0 === \wp_get_update_data()['counts']['total'] ) {
			return $tasks;
		}

		$inject_items = [
			[
				'task_id'               => 'update-core',
				'title'                 => \esc_html__( 'Perform all updates', 'progress-planner' ),
				'parent'                => 0,
				'priority'              => 'high',
				'type'                  => 'maintenance',
				'premium'               => 'no',
				'description'           => '<p>' . \esc_html__( 'Perform all updates to ensure your website is secure and up-to-date.', 'progress-planner' ) . '</p>',
				'completion_type'       => 'auto',
				'evaluation_conditions' => false,
			],
		];
		return \array_merge( $inject_items, $tasks );
	}
}
