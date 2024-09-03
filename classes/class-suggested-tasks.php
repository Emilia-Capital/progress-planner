<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

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
	}

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		add_action( 'wp_ajax_progress_planner_dismiss_task', [ $this, 'dismiss' ] );
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
	 * Get the dismissed tasks.
	 *
	 * @return array
	 */
	public static function get_dismissed_tasks() {
		$option = \get_option( self::OPTION_NAME, [] );
		return $option['dismissed'] ?? [];
	}
}
