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

		\add_action( 'init', [ $this, 'init' ], 1 );
	}

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	public function init() {
		// WIP: Methods which call class which instantiates this class need to be delayed.
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
		$tasks = $this->get_pending_tasks();
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}

		$tasks = \array_unique( $tasks );
		foreach ( $tasks as $task ) {
			if ( $this->evaluate_task( $task ) ) {
				$this->remove_pending_task( $task );
			}
		}
	}

	/**
	 * Evaluate a task.
	 *
	 * @param string $task The task ID.
	 *
	 * @return bool
	 */
	abstract protected function evaluate_task( $task );

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
