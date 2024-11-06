<?php
/**
 * Handle suggested tasks.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

/**
 * Suggested_Tasks class.
 */
class Suggested_Tasks {

	/**
	 * An object containing local tasks.
	 *
	 * @var \stdClass
	 */
	private $local;

	/**
	 * The API object.
	 *
	 * @var \Progress_Planner\Suggested_Tasks\API|null
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
		$this->local                 = new \stdClass();
		$this->local->update_content = new \Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Content();
		$this->local->update_core    = new \Progress_Planner\Suggested_Tasks\Local_Tasks\Update_Core();

		$this->maybe_unsnooze_tasks();
		\add_action( 'shutdown', [ $this, 'maybe_celebrate_tasks' ] );
		\add_action( 'wp_ajax_progress_planner_suggested_task_action', [ $this, 'suggested_task_action' ] );
	}

	/**
	 * Get the API object.
	 *
	 * @return \Progress_Planner\Suggested_Tasks\API
	 */
	public function get_api() {
		if ( ! $this->api ) {
			$this->api = new \Progress_Planner\Suggested_Tasks\API();
		}
		return $this->api;
	}

	/**
	 * Get the local tasks object.
	 *
	 * @return \stdClass
	 */
	public function get_local() {
		return $this->local;
	}

	/**
	 * Mark a task as completed.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return void
	 */
	public function mark_task_as_completed( $task_id ) {
		$activity          = new \Progress_Planner\Activities\Suggested_Task();
		$activity->type    = 'completed';
		$activity->data_id = (string) $task_id;
		$activity->date    = new \DateTime();
		$activity->user_id = \get_current_user_id();
		$activity->save();

		$this->mark_task_as_pending_celebration( $task_id );
	}

	/**
	 * Get pending celebration tasks.
	 *
	 * @return array
	 */
	public function get_pending_celebration() {
		$option = \get_option( self::OPTION_NAME, [] );
		return $option['pending_celebration'] ?? [];
	}

	/**
	 * Mark a task as pending celebration.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool
	 */
	public function mark_task_as_pending_celebration( $task_id ) {
		$option                        = \get_option( self::OPTION_NAME, [] );
		$option['pending_celebration'] = isset( $option['pending_celebration'] )
			? $option['pending_celebration']
			: [];
		if ( \in_array( $task_id, $option['pending_celebration'], true ) ) {
			return false;
		}
		$option['pending_celebration'][] = (string) $task_id;
		return \update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Mark a task as celebrated.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool
	 */
	public function mark_task_as_celebrated( $task_id ) {
		$option                        = \get_option( self::OPTION_NAME, [] );
		$option['pending_celebration'] = isset( $option['pending_celebration'] )
			? $option['pending_celebration']
			: [];
		if ( ! \in_array( $task_id, $option['pending_celebration'], true ) ) {
			return false;
		}
		unset( $option['pending_celebration'][ \array_search( $task_id, $option['pending_celebration'], true ) ] );
		return \update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Maybe celebrate tasks.
	 *
	 * @return void
	 */
	public function maybe_celebrate_tasks() {
		$option                        = \get_option( self::OPTION_NAME, [] );
		$option['pending_celebration'] = isset( $option['pending_celebration'] )
			? $option['pending_celebration']
			: [];

		if ( empty( $option['pending_celebration'] ) ) {
			return;
		}

		$option['pending_celebration'] = [];
		\update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Mark a task as snoozed.
	 *
	 * @param string $task_id The task ID.
	 * @param string $duration The duration.
	 *
	 * @return bool
	 */
	public function mark_task_as_snoozed( $task_id, $duration ) {
		$option  = \get_option( self::OPTION_NAME, [] );
		$snoozed = $option['snoozed'] ?? [];

		switch ( $duration ) {
			case '1-month':
				$time = \MONTH_IN_SECONDS;
				break;

			case '3-months':
				$time = 3 * \MONTH_IN_SECONDS;
				break;

			case '6-months':
				$time = 6 * \MONTH_IN_SECONDS;
				break;

			case '1-year':
				$time = \YEAR_IN_SECONDS;
				break;

			case 'forever':
				$time = \PHP_INT_MAX;
				break;

			default:
				$time = \WEEK_IN_SECONDS;
				break;
		}

		// Check if there's already an item with the same ID.
		$item_exists = false;
		foreach ( $snoozed as $key => $snoozed_task ) {
			if ( $snoozed_task['id'] === $task_id ) {
				$snoozed[ $key ]['time'] = \time() + $time;
				$item_exists             = true;
				break;
			}
		}

		if ( ! $item_exists ) {
			$snoozed[] = [
				'id'   => (string) $task_id,
				'time' => \time() + $time,
			];
		}
		$option['snoozed'] = $snoozed;

		return \update_option( self::OPTION_NAME, $option );
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

		$update = false;
		foreach ( $option['snoozed'] as $key => $task ) {
			if ( $task['time'] < $current_time ) {
				unset( $option['snoozed'][ $key ] );
				$update = true;
			}
		}
		if ( $update ) {
			\update_option( self::OPTION_NAME, $option );
		}
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
}
