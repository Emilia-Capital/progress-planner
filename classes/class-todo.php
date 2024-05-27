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
class Todo {

	/**
	 * The name of the settings option.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'progress_planner_todo';

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
		add_action( 'wp_ajax_progress_planner_save_todo_list', [ $this, 'save' ] );
	}

	/**
	 * Get the todo list items.
	 *
	 * @return array
	 */
	public static function get_items() {
		$value = get_option( self::OPTION_NAME, [] );
		foreach ( $value as $key => $item ) {
			if ( ! isset( $item['content'] ) || empty( $item['content'] ) ) {
				unset( $value[ $key ] );
			}
		}
		return array_values( $value );
	}

	/**
	 * Get pending items.
	 *
	 * @return array
	 */
	public static function get_pending_items() {
		$items = self::get_items();
		$pending = [];
		foreach ( $items as $item ) {
			if ( ! $item['done'] ) {
				$pending[] = $item;
			}
		}
		return $pending;
	}

	/**
	 * Save the todo list.
	 *
	 * @return void
	 */
	public function save() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner_todo', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		if ( ! isset( $_POST['todo_list'] ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Missing data.', 'progress-planner' ) ] );
		}

		error_log( print_r( $_POST['todo_list'], true ) );
		$items = [];
		if ( ! empty( $_POST['todo_list'] ) ) {
			foreach ( array_values( $_POST['todo_list'] ) as $item ) {
				$items[] = [
					'content' => sanitize_text_field( $item['content'] ),
					'done'    => true === $item['done'] || 'true' === $item['done'],
				];
			}
		}

		if ( ! \update_option( self::OPTION_NAME, $items ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Failed to save.', 'progress-planner' ) ] );
		}
		\wp_send_json_success( [ 'message' => \esc_html__( 'Saved.', 'progress-planner' ) ] );
	}
}
