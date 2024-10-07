<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

use Progress_Planner\Suggested_Tasks\API as Suggested_Tasks_API;
use Progress_Planner\Suggested_Tasks;

/**
 * Settings class.
 */
class Scripts {

	/**
	 * The API object.
	 *
	 * @var Suggested_Tasks_API
	 */
	private $api;

	/**
	 * The name of the settings option.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'progress_planner_suggested_tasks';

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->api = new Suggested_Tasks_API();
		$this->register_hooks();
		$this->register_scripts();
	}

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		\add_action( 'wp_ajax_progress_planner_suggested_task_action', [ $this, 'suggested_task_action' ] );
		\add_action( 'progress_planner_activity_saved', [ $this, 'activity_saved' ] );
	}

	/**
	 * Handle the suggested task action.
	 *
	 * @return void
	 */
	public function suggested_task_action() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		if ( ! isset( $_POST['task_id'] ) || ! isset( $_POST['action_type'] ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Missing data.', 'progress-planner' ) ] );
		}

		$action  = \sanitize_text_field( \wp_unslash( $_POST['action_type'] ) );
		$task_id = (int) \sanitize_text_field( \wp_unslash( $_POST['task_id'] ) );

		switch ( $action ) {
			case 'complete':
				$updated = Suggested_Tasks::mark_task_as_completed( $task_id );
				break;

			case 'snooze':
				$updated = Suggested_Tasks::mark_task_as_snoozed( $task_id );
				break;

			case 'dismiss':
				$updated = Suggested_Tasks::mark_task_as_dismissed( $task_id );
				break;

			default:
				\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid action.', 'progress-planner' ) ] );
		}

		if ( ! $updated ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Failed to save.', 'progress-planner' ) ] );
		}

		\wp_send_json_success( [ 'message' => \esc_html__( 'Saved.', 'progress-planner' ) ] );
	}

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts() {
		\wp_register_script(
			'progress-planner-suggested-tasks',
			PROGRESS_PLANNER_URL . '/assets/js/suggested-tasks.js',
			[ 'progress-planner-todo' ],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/suggested-tasks.js' ),
			true
		);
		$tasks            = $this->api->get_saved_tasks();
		$tasks['details'] = $this->api->get_tasks();
		$localize_data    = [
			'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
			'nonce'   => \wp_create_nonce( 'progress_planner' ),
			'tasks'   => $tasks,
		];
		\wp_localize_script( 'progress-planner-suggested-tasks', 'progressPlannerSuggestedTasks', $localize_data );
	}
}
