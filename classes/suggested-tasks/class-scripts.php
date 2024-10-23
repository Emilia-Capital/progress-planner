<?php
/**
 * Handle scripts for the Suggested Tasks.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

use Progress_Planner\Suggested_Tasks\API as Suggested_Tasks_API;

/**
 * Handle scripts for the Suggested Tasks.
 */
class Scripts {

	/**
	 * The API object.
	 *
	 * @var Suggested_Tasks_API
	 */
	private $api;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->api = new Suggested_Tasks_API();
		$this->register_hooks();
	}

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		\add_action( 'wp_ajax_progress_planner_suggested_task_action', [ $this, 'suggested_task_action' ] );
		\add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
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
		$task_id = (string) \sanitize_text_field( \wp_unslash( $_POST['task_id'] ) );

		switch ( $action ) {
			case 'complete':
				\progress_planner()->get_suggested_tasks()->mark_task_as_completed( $task_id );
				$updated = true;
				break;

			case 'snooze':
				$duration = isset( $_POST['duration'] ) ? \sanitize_text_field( \wp_unslash( $_POST['duration'] ) ) : '';
				$updated  = \progress_planner()->get_suggested_tasks()->mark_task_as_snoozed( $task_id, $duration );
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
			'particles-confetti-js',
			PROGRESS_PLANNER_URL . '/assets/js/vendor/tsparticles.confetti.bundle.min.js',
			[],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/vendor/tsparticles.confetti.bundle.min.js' ),
			true
		);

		$pending_celebration = \progress_planner()->get_suggested_tasks()->get_pending_celebration();
		$deps                = [ 'progress-planner-todo', 'progress-planner-grid-masonry' ];
		if ( ! empty( $pending_celebration ) ) {
			$deps[] = 'particles-confetti-js';
		}

		\wp_register_script(
			'progress-planner-suggested-tasks',
			PROGRESS_PLANNER_URL . '/assets/js/suggested-tasks.js',
			$deps,
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
