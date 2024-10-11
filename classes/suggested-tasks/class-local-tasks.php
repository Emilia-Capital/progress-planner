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
	 * An array of tasks to inject.
	 *
	 * @var array
	 */
	protected $tasks_to_inject = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'progress_planner_suggested_tasks_api_items', [ $this, 'inject_tasks' ] );
		$this->evaluate_tasks();
	}

	/**
	 * Inject tasks.
	 *
	 * @param array $tasks The tasks.
	 *
	 * @return array
	 */
	public function inject_tasks( $tasks ) {
		$inject_items = $this->get_tasks_to_inject();
		if ( ! is_array( $inject_items ) ) {
			$inject_items = [];
		}
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}
		return \array_merge( $inject_items, $tasks );
	}

	/**
	 * Get tasks to inject.
	 *
	 * @return array
	 */
	abstract protected function get_tasks_to_inject();

	/**
	 * Evaluate tasks stored in the option.
	 *
	 * @return void
	 */
	private function evaluate_tasks() {
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
