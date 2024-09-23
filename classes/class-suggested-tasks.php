<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Activities\Suggested_Task as Suggested_Task_Activity;

/**
 * Settings class.
 */
class Suggested_Tasks {

	/**
	 * The remote server URL.
	 *
	 * @var string
	 */
	const REMOTE_DOMAIN = 'https://progressplanner.com';

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
		$this->register_hooks();
		$this->maybe_unsnooze_tasks();
		$this->register_scripts();
	}

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		add_action( 'wp_ajax_progress_planner_suggested_task_action', [ $this, 'suggested_task_action' ] );
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

		$action = \sanitize_text_field( \wp_unslash( $_POST['action_type'] ) );
		$option = \get_option( self::OPTION_NAME, [] );

		switch ( $action ) {
			case 'complete':
				$activity          = new Suggested_Task_Activity();
				$activity->type    = 'completed';
				$activity->data_id = 0;
				$activity->date    = new \DateTime();
				$activity->user_id = get_current_user_id();
				$activity->save();
				$completed           = $option['completed'] ?? [];
				$completed[]         = \sanitize_text_field( \wp_unslash( $_POST['task_id'] ) );
				$option['completed'] = $completed;
				break;

			case 'snooze':
				$snoozed           = $option['snoozed'] ?? [];
				$snoozed[]         = [
					'id'   => \sanitize_text_field( \wp_unslash( $_POST['task_id'] ) ),
					'time' => \time() + \WEEK_IN_SECONDS,
				];
				$option['snoozed'] = $snoozed;
				break;

			case 'dismiss':
				$dismissed           = $option['dismissed'] ?? [];
				$dismissed[]         = \sanitize_text_field( \wp_unslash( $_POST['task_id'] ) );
				$option['dismissed'] = $dismissed;
				break;

			default:
				\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid action.', 'progress-planner' ) ] );
		}

		if ( ! \update_option( self::OPTION_NAME, $option ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Failed to save.', 'progress-planner' ) ] );
		}

		\wp_send_json_success( [ 'message' => \esc_html__( 'Saved.', 'progress-planner' ) ] );
	}

	/**
	 * Get the premium to-do items.
	 *
	 * @return array
	 */
	public function get_tasks() {
		// Check if we have a cached response.
		$items = \get_transient( 'progress_planner_suggested_tasks' );

		// If we have a cached response, return it.
		if ( $items ) {
			return $items;
		}

		$remote_url = self::REMOTE_DOMAIN . '/wp-json/progress-planner-saas/v1/suggested-todo/';

		// Get the response from the remote server.
		$response = \wp_remote_get( $remote_url );

		// Bail if the request failed.
		if ( \is_wp_error( $response ) ) {
			return [];
		}

		// Get the body of the response.
		$body = \wp_remote_retrieve_body( $response );

		// Bail if the body is empty.
		if ( empty( $body ) ) {
			return [];
		}

		// Decode the JSON body.
		$data = \json_decode( $body, true );

		// Bail if the JSON decoding failed.
		if ( ! \is_array( $data ) ) {
			return [];
		}

		// Cache the response for 1 day.
		\set_transient( 'progress_planner_suggested_tasks', $data, DAY_IN_SECONDS );

		return $data;
	}

	/**
	 * Get an array of completed, dismissed, and snoozed tasks.
	 *
	 * @return array
	 */
	public function get_saved_tasks() {
		$option              = \get_option( self::OPTION_NAME, [] );
		$option['completed'] = $option['completed'] ?? [];
		$option['dismissed'] = $option['dismissed'] ?? [];
		$option['snoozed']   = $option['snoozed'] ?? [];
		return $option;
	}

	/**
	 * Maybe unsnooze tasks.
	 *
	 * @return void
	 */
	private function maybe_unsnooze_tasks() {
		$option = \get_option( self::OPTION_NAME, [] );
		if ( ! isset( $option['snoozed'] ) ) {
			return;
		}
		$current_time = \time();

		foreach ( $option['snoozed'] as $key => $task ) {
			if ( $task['time'] < $current_time ) {
				unset( $option['snoozed'][ $key ] );
			}
		}
		\update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts() {
		\wp_enqueue_script(
			'progress-planner-suggested-tasks',
			PROGRESS_PLANNER_URL . '/assets/js/suggested-tasks.js',
			[ 'progress-planner-todo' ],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/suggested-tasks.js' ),
			true
		);
		$tasks            = $this->get_saved_tasks();
		$tasks['details'] = $this->get_tasks();
		$localize_data    = [
			'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
			'nonce'   => \wp_create_nonce( 'progress_planner' ),
			'tasks'   => $tasks,
		];
		\wp_localize_script( 'progress-planner-suggested-tasks', 'progressPlannerSuggestedTasks', $localize_data );
	}
}
