<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Activities\Todo as Todo_Activity;

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

		if ( $_POST['todo_list'] === 'empty' ) {
			\delete_option( self::OPTION_NAME );
			\wp_send_json_success( [ 'message' => \esc_html__( 'Saved.', 'progress-planner' ) ] );	
		}

		$items          = [];
		$previous_items = self::get_items();

		if ( ! empty( $_POST['todo_list'] ) ) {
			foreach ( array_values( wp_unslash( $_POST['todo_list'] ) ) as $item ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$items[] = [
					'content' => sanitize_text_field( $item['content'] ),
					'done'    => true === $item['done'] || 'true' === $item['done'],
				];
			}
		}

		if ( ! \update_option( self::OPTION_NAME, $items ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Failed to save.', 'progress-planner' ) ] );
		}

		// Save the activity.
		$activity       = new Todo_Activity();
		$activity->type = 'update';
		if ( count( $items ) > count( $previous_items ) ) {
			$activity->type = 'add';
		} elseif ( count( $items ) < count( $previous_items ) ) {
			$activity->type = 'delete';
		}
		$activity->save();

		\wp_send_json_success( [ 'message' => \esc_html__( 'Saved.', 'progress-planner' ) ] );
	}
}
