<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

/**
 * Settings class.
 */
abstract class Local_Tasks {

	/**
	 * The option name, holding pending local tasks.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'progress_planner_local_tasks';

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'progress_planner_suggested_tasks_api_items', [ $this, 'inject_tasks' ] );
		$this->evaluate_previous_tasks();
	}

	/**
	 * Inject tasks.
	 *
	 * @param array $tasks The tasks.
	 *
	 * @return array
	 */
	abstract public function inject_tasks( $tasks );

	/**
	 * Evaluate previous tasks.
	 *
	 * @return void
	 */
	private function evaluate_previous_tasks() {
		$tasks = self::get_tasks();
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}
		foreach ( $tasks as $task ) {
			$this->evaluate_task( $task );
		}
	}

	/**
	 * Evaluate a task.
	 *
	 * @param string $task The task ID.
	 *
	 * @return void
	 */
	abstract protected function evaluate_task( $task );

	/**
	 * Get pending local tasks.
	 *
	 * @return array
	 */
	public static function get_tasks() {
		return \get_option( self::OPTION_NAME, [] );
	}

	/**
	 * Add a pending local task.
	 *
	 * @param string $task The task ID.
	 *
	 * @return bool
	 */
	public static function add_pending_task( $task ) {
		$tasks = self::get_tasks();
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
	public static function remove_pending_task( $task ) {
		$tasks = self::get_tasks();
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}
		$tasks = \array_diff( $tasks, [ $task ] );
		return \update_option( self::OPTION_NAME, $tasks );
	}
}
