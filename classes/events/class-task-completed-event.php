<?php
/**
 * Task completed event.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Events;

/**
 * User registered event.
 */
class Task_Completed_Event {
	/**
	 * The task ID.
	 *
	 * @var int
	 */
	public $task_id;

	/**
	 * Constructor.
	 *
	 * @param int $task_id The task ID.
	 */
	public function __construct( $task_id ) {
		$this->task_id = $task_id;
	}
}
