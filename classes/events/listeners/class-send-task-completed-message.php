<?php
/**
 * Send welcome message listener.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Events\Listeners;

/**
 * Send welcome message listener.
 */
class Send_Task_Completed_Message {

	/**
	 * Handle the event.
	 *
	 * @param object $event The Task_Completed_Event event.
	 * @return void
	 */
	public function handle( $event ) {
		$task_id = $event->task_id;

		// Simulate sending a welcome message.
		error_log( 'Completed task: ' . $task_id );
	}
}
