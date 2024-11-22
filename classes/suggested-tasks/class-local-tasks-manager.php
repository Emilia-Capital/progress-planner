<?php
/**
 * Handle suggested local tasks.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

/**
 * Local_Tasks_Manager class.
 */
class Local_Tasks_Manager {

	/**
	 * The option name, holding pending local tasks.
	 *
	 * We're using an option to store these tasks,
	 * because otherwise we have no way to keep track of
	 * what was completed in order to award points.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'progress_planner_local_tasks';

	/**
	 * The update content task.
	 *
	 * @var \Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Content
	 */
	private $update_content;

	/**
	 * The update core task.
	 *
	 * @var \Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Core
	 */
	private $update_core;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->update_content = new \Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Content();
		$this->update_core    = new \Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Core();

		\add_filter( 'progress_planner_suggested_tasks_items', [ $this, 'inject_tasks' ] );
	}

	/**
	 * Get the update content task.
	 *
	 * @return \Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Content
	 */
	public function get_update_content() {
		return $this->update_content;
	}

	/**
	 * Get the update core task.
	 *
	 * @return \Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Core
	 */
	public function get_update_core() {
		return $this->update_core;
	}

	/**
	 * Inject tasks.
	 *
	 * @param array $tasks The tasks.
	 *
	 * @return array
	 */
	public function inject_tasks( $tasks ) {
		$tasks_to_inject = \array_merge(
			$this->update_content->get_tasks_to_inject(),
			$this->update_core->get_tasks_to_inject()
		);

		// Add the tasks to the pending tasks option, it will not add duplicates.
		foreach ( $tasks_to_inject as $task ) {
			$this->add_pending_task( $task['task_id'] );
		}

		return \array_merge( $tasks, $tasks_to_inject );
	}

	/**
	 * Evaluate tasks stored in the option.
	 *
	 * @return array
	 */
	public function evaluate_tasks() {
		$tasks = $this->get_pending_tasks();
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}

		$completed_tasks = [];

		$tasks = \array_unique( $tasks );
		foreach ( $tasks as $task_id ) {

			$task_result = $this->evaluate_task( $task_id );

			if ( false !== $task_result ) {
				$this->remove_pending_task( $task_id );
				$completed_tasks[] = $task_id;
			}
		}

		return $completed_tasks;
	}

	/**
	 * Wrapper function for evaluating tasks.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool|string
	 */
	public function evaluate_task( $task_id ) {
		if ( \str_contains( $task_id, '|' ) ) {
			return $this->update_content->evaluate_task( $task_id );
		} else {
			return $this->update_core->evaluate_task( $task_id );
		}
	}

	/**
	 * Wrapper function for getting task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_task_details( $task_id ) {
		if ( \str_contains( $task_id, '|' ) ) {
			return $this->update_content->get_task_details( $task_id );
		} else {
			return $this->update_core->get_task_details( $task_id );
		}
	}

	/**
	 * Wrapper function for getting task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_data_from_task_id( $task_id ) {
		if ( \str_contains( $task_id, '|' ) ) {
			return $this->update_content->get_data_from_task_id( $task_id );
		} else {
			return $this->update_core->get_data_from_task_id( $task_id );
		}
	}



	/**
	 * Get pending local tasks.
	 *
	 * @return array
	 */
	public function get_pending_tasks() {
		return \get_option( self::OPTION_NAME, [] );
	}

	/**
	 * Add a pending local task.
	 *
	 * @param string $task The task ID.
	 *
	 * @return bool
	 */
	public function add_pending_task( $task ) {
		$tasks = $this->get_pending_tasks();
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}
		if ( \in_array( $task, $tasks, true ) ) {
			return true;
		}
		$tasks[] = $task;
		return \update_option( self::OPTION_NAME, $tasks );
	}

	/**
	 * Remove a pending local task.
	 *
	 * @param string $task The task ID.
	 *
	 * @return bool
	 */
	public function remove_pending_task( $task ) {
		$tasks = $this->get_pending_tasks();
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}
		$tasks = \array_diff( $tasks, [ $task ] );
		return \update_option( self::OPTION_NAME, $tasks );
	}
}
