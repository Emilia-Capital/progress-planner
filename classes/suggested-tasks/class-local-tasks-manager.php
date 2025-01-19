<?php
/**
 * Handle suggested local tasks.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

use Progress_Planner\Suggested_Tasks\Local_Tasks\Local_Task_Factory;
use Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Content_Create;
use Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Content_Update;
use Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Core_Update;
use Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Core_Blogdescription;
use Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Settings_Saved;
use Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Yoast_Organization_Logo;


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
	 * The task providers.
	 *
	 * @var array
	 */
	private $task_providers = [];

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->task_providers = [
			new Content_Create(),
			new Content_Update(),
			new Core_Update(),
			new Core_Blogdescription(),
			new Settings_Saved(),
		];

		\add_filter( 'progress_planner_suggested_tasks_items', [ $this, 'inject_tasks' ] );
		\add_action( 'plugins_loaded', [ $this, 'add_plugin_integration' ] );

		// Add the cleanup action.
		\add_action( 'admin_init', [ $this, 'cleanup_pending_tasks' ] );
	}

	/**
	 * Add the Yoast task if the plugin is active.
	 *
	 * @return void
	 */
	public function add_plugin_integration() {
		if ( defined( 'WPSEO_FILE' ) ) {
			$this->task_providers[] = new Yoast_Organization_Logo();
		}
	}

	/**
	 * Get a task provider by its type.
	 *
	 * @param string $name The method name.
	 * @param array  $arguments The arguments.
	 *
	 * @return \Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Local_Tasks_Interface|null
	 */
	public function __call( $name, $arguments ) {
		if ( 0 === strpos( $name, 'get_' ) ) {
			$provider_type = substr( $name, 4 ); // Remove 'get_' prefix.
			$provider_type = str_replace( '_', '-', strtolower( $provider_type ) ); // Transform 'update_core' to 'update-core'.

			return $this->get_task_provider( $provider_type );
		}

		return null;
	}

	/**
	 * Get a task provider by its type.
	 *
	 * @param string $provider_type The provider type.
	 *
	 * @return \Progress_Planner\Suggested_Tasks\Local_Tasks\Providers\Local_Tasks_Interface|null
	 */
	public function get_task_provider( $provider_type ) {
		foreach ( $this->task_providers as $provider_instance ) {
			if ( $provider_instance->get_provider_type() === $provider_type ) {
				return $provider_instance;
			}
		}

		return null;
	}

	/**
	 * Inject tasks.
	 *
	 * @param array $tasks The tasks.
	 *
	 * @return array
	 */
	public function inject_tasks( $tasks ) {
		$tasks_to_inject = [];

		// Loop through all registered task providers and inject their tasks.
		foreach ( $this->task_providers as $provider_instance ) {
			$tasks_to_inject = \array_merge( $tasks_to_inject, $provider_instance->get_tasks_to_inject() );
		}

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
		$tasks           = (array) $this->get_pending_tasks();
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
		$task_object   = ( new Local_Task_Factory( $task_id ) )->get_task();
		$task_provider = $this->get_task_provider( $task_object->get_provider_type() );

		if ( ! $task_provider ) {
			return false;
		}

		return $task_provider->evaluate_task( $task_id );
	}

	/**
	 * Removes pending tasks for which providers are not (longer) active.
	 *
	 * @return void
	 */
	public function cleanup_pending_tasks() {
		$tasks = $this->get_pending_tasks();

		foreach ( $tasks as $task ) {
			$task_object = ( new Local_Task_Factory( $task ) )->get_task();
			$provider    = $this->get_task_provider( $task_object->get_provider_type() );
			if ( ! $provider ) {
				$this->remove_pending_task( $task );
			}
		}
	}

	/**
	 * Wrapper function for getting task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array|false
	 */
	public function get_task_details( $task_id ) {
		$task_object   = ( new Local_Task_Factory( $task_id ) )->get_task();
		$task_provider = $this->get_task_provider( $task_object->get_provider_type() );

		if ( ! $task_provider ) {
			return false;
		}

		return $task_provider->get_task_details( $task_id );
	}

	/**
	 * Wrapper function for getting task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_data_from_task_id( $task_id ) {
		$task_object = ( new Local_Task_Factory( $task_id ) )->get_task();

		return $task_object->get_data();
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
		$tasks = (array) $this->get_pending_tasks();
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
		$tasks = (array) $this->get_pending_tasks();
		$tasks = \array_diff( $tasks, [ $task ] );
		return \update_option( self::OPTION_NAME, $tasks );
	}

	/**
	 * Remove all tasks which have date set to the previous week.
	 * Tasks for the current week will be added automatically.
	 *
	 * @return void
	 */
	public function cleanup_pending_tasks() {

		$cleanup_recently_performed = \progress_planner()->get_cache()->get( 'cleanup_pending_tasks' );

		if ( $cleanup_recently_performed ) {
			return;
		}

		$tasks = (array) $this->get_pending_tasks();

		if ( empty( $tasks ) ) {
			return;
		}

		$task_count = count( $tasks );

		$tasks = \array_filter(
			$tasks,
			function ( $task ) {
				$task_object = ( new Local_Task_Factory( $task ) )->get_task();
				$task_data   = $task_object->get_data();

				if ( isset( $task_data['year_week'] ) ) {
					return \gmdate( 'YW' ) === $task_data['year_week'];
				}

				return true;
			}
		);

		if ( count( $tasks ) !== $task_count ) {
			\update_option( self::OPTION_NAME, $tasks );
		}

		\progress_planner()->get_cache()->set( 'cleanup_pending_tasks', true, DAY_IN_SECONDS );
	}
}
