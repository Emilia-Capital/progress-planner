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
	}

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		add_action( 'wp_ajax_progress_planner_dismiss_task', [ $this, 'dismiss' ] );
		add_action( 'wp_ajax_progress_planner_snooze_task', [ $this, 'snooze' ] );
		add_action( 'wp_ajax_progress_planner_complete_task', [ $this, 'snooze' ] );
	}

	/**
	 * Mark a task as completed.
	 *
	 * @return void
	 */
	public function complete() {
		$activity          = new Suggested_Task_Activity();
		$activity->type    = 'completed';
		$activity->data_id = 0;
		$activity->date    = new \DateTime();
		$activity->user_id = get_current_user_id();
		$activity->save();

		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		if ( ! isset( $_POST['task_id'] ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Missing data.', 'progress-planner' ) ] );
		}

		$option      = \get_option( self::OPTION_NAME, [] );
		$completed   = $option['completed'] ?? [];
		$completed[] = \sanitize_text_field( \wp_unslash( $_POST['task_id'] ) );

		$option['completed'] = $completed;

		if ( ! \update_option( self::OPTION_NAME, $option ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Failed to save.', 'progress-planner' ) ] );
		}

		\wp_send_json_success( [ 'message' => \esc_html__( 'Saved.', 'progress-planner' ) ] );
	}

	/**
	 * Snooze a task.
	 *
	 * @return void
	 */
	public function snooze() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		if ( ! isset( $_POST['task_id'] ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Missing data.', 'progress-planner' ) ] );
		}

		$option    = \get_option( self::OPTION_NAME, [] );
		$snoozed   = $option['snoozed'] ?? [];
		$snoozed[] = [
			'id'   => \sanitize_text_field( \wp_unslash( $_POST['task_id'] ) ),
			'time' => \time() + \WEEK_IN_SECONDS,
		];

		$option['snoozed'] = $snoozed;

		if ( ! \update_option( self::OPTION_NAME, $option ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Failed to save.', 'progress-planner' ) ] );
		}

		\wp_send_json_success( [ 'message' => \esc_html__( 'Saved.', 'progress-planner' ) ] );
	}

	/**
	 * Save the todo list.
	 *
	 * @return void
	 */
	public function dismiss() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		if ( ! isset( $_POST['task_id'] ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Missing data.', 'progress-planner' ) ] );
		}

		$option      = \get_option( self::OPTION_NAME, [] );
		$dismissed   = $option['dismissed'] ?? [];
		$dismissed[] = \sanitize_text_field( \wp_unslash( $_POST['task_id'] ) );

		$option['dismissed'] = $dismissed;

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
	public static function get_tasks() {
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
	 * Get the completed tasks.
	 *
	 * @return array
	 */
	public static function get_completed_tasks() {
		$option = \get_option( self::OPTION_NAME, [] );
		return $option['completed'] ?? [];
	}

	/**
	 * Get the dismissed tasks.
	 *
	 * @return array
	 */
	public static function get_dismissed_tasks() {
		$option = \get_option( self::OPTION_NAME, [] );
		return $option['dismissed'] ?? [];
	}

	/**
	 * Get an array of snoozed tasks.
	 *
	 * @return array
	 */
	public static function get_snoozed_tasks() {
		$option = \get_option( self::OPTION_NAME, [] );
		return $option['snoozed'] ?? [];
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
}
