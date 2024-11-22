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
	 * @var \Progress_Planner\Suggested_Tasks\Local_Tasks_Manager|null
	 */
	private $local;

	/**
	 * The API object.
	 *
	 * @var \Progress_Planner\Suggested_Tasks\Remote_Tasks|null
	 */
	private $remote;

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
		$this->local  = new \Progress_Planner\Suggested_Tasks\Local_Tasks_Manager();
		$this->remote = new \Progress_Planner\Suggested_Tasks\Remote_Tasks();

		\add_action( 'wp_ajax_progress_planner_suggested_task_action', [ $this, 'suggested_task_action' ] );

		if ( \is_admin() ) {
			\add_action( 'init', [ $this, 'init' ], 1 );
		}
	}

	/**
	 * Run the local tasks.
	 *
	 * @return void
	 */
	public function init() {
		// Unsnooze tasks.
		$this->maybe_unsnooze_tasks();

		// Check for completed tasks.
		$completed_tasks = $this->local->evaluate_tasks();

		foreach ( $completed_tasks as $task_id ) {
			$this->mark_task_as_pending_celebration( $task_id );

			// Insert an activity.
			$activity          = new \Progress_Planner\Activities\Suggested_Task();
			$activity->type    = 'completed';
			$activity->data_id = (string) $task_id;
			$activity->date    = new \DateTime();
			$activity->user_id = \get_current_user_id();
			$activity->save();

			// Allow other classes to react to the completion of a suggested task.
			do_action( 'progress_planner_suggested_task_completed', $task_id );
		}
	}

	/**
	 * Get the API object.
	 *
	 * @return \Progress_Planner\Suggested_Tasks\Remote_Tasks
	 */
	public function get_remote() {
		return $this->remote;
	}

	/**
	 * Get the local tasks object.
	 *
	 * @return \Progress_Planner\Suggested_Tasks\Local_Tasks_Manager
	 */
	public function get_local() {
		return $this->local;
	}

	/**
	 * Return filtered items.
	 *
	 * @return array
	 */
	public function get_tasks() {
		$tasks = [];
		/**
		 * Filter the suggested tasks.
		 *
		 * @param array $tasks The suggested tasks.
		 * @return array
		 */
		return \apply_filters( 'progress_planner_suggested_tasks_items', $tasks );
	}

	/**
	 * Get an array of completed and snoozed tasks.
	 *
	 * @return array
	 */
	public function get_saved_tasks() {
		$option                        = \get_option( self::OPTION_NAME, [] );
		$option['completed']           = $option['completed'] ?? [];
		$option['snoozed']             = $option['snoozed'] ?? [];
		$option['pending_celebration'] = $option['pending_celebration'] ?? [];

		// Convert the task IDs to strings.
		$option['completed']           = \array_map( 'strval', $option['completed'] );
		$option['pending_celebration'] = \array_map( 'strval', $option['pending_celebration'] );
		$option['snoozed']             = \array_map(
			function ( $task ) {
				return [
					'id'   => (string) $task['id'],
					'time' => (int) $task['time'],
				];
			},
			$option['snoozed']
		);

		// Remove items with id 0.
		$option['completed']           = \array_values( \array_filter( $option['completed'] ) );
		$option['pending_celebration'] = \array_values( \array_filter( $option['pending_celebration'] ) );
		$option['snoozed']             = \array_values(
			\array_filter(
				$option['snoozed'],
				function ( $task ) {
					return $task['id'] > 0;
				}
			)
		);
		return $option;
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
	 * Mark a task as completed.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool
	 */
	public function mark_task_as_completed( $task_id ) {
		return $this->mark_task_as( 'completed', $task_id );
	}

	/**
	 * Mark a task as pending celebration.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool
	 */
	public function mark_task_as_pending_celebration( $task_id ) {
		return $this->mark_task_as( 'pending_celebration', $task_id );
	}

	/**
	 * Mark a task as snoozed.
	 *
	 * @param string $task_id The task ID.
	 * @param int    $time The time.
	 *
	 * @return bool
	 */
	public function mark_task_as_snoozed( $task_id, $time ) {
		return $this->mark_task_as( 'snoozed', $task_id, [ 'time' => $time ] );
	}

	/**
	 * Mark a task as a given status.
	 *
	 * @param string $status The status.
	 * @param string $task_id The task ID.
	 * @param array  $data The data.
	 *
	 * @return bool
	 */
	public function mark_task_as( $status, $task_id, $data = [] ) {
		$option            = \get_option( self::OPTION_NAME, [] );
		$option[ $status ] = isset( $option[ $status ] )
			? $option[ $status ]
			: [];

		// Check if there's already an item with the same ID.
		if ( 'snoozed' === $status ) {
			$item_exists = false;
			foreach ( $option[ $status ] as $key => $snoozed_task ) {
				if ( $snoozed_task['id'] === $task_id ) {

					// If task is already snoozed, update the time.
					$option[ $status ][ $key ]['time'] = \time() + $data['time'];
					$item_exists                       = true;
					break;
				}
			}

			// If task is not snoozed, add it.
			if ( ! $item_exists ) {
				$option[ $status ][] = [
					'id'   => (string) $task_id,
					'time' => \time() + $data['time'],
				];
			}
		} else {
			if ( \in_array( $task_id, $option[ $status ], true ) ) {
				return false;
			}

			$option[ $status ][] = (string) $task_id;
		}

		return \update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Mark a task as celebrated.
	 *
	 * @param string $status The status.
	 * @param string $task_id The task ID.
	 *
	 * @return bool
	 */
	public function remove_task_from( $status, $task_id ) {
		$option            = \get_option( self::OPTION_NAME, [] );
		$option[ $status ] = isset( $option[ $status ] )
			? $option[ $status ]
			: [];
		$remove_index      = false;

		if ( 'snoozed' === $status ) {
			foreach ( $option[ $status ] as $key => $task ) {
				if ( $task['id'] === $task_id ) {
					$remove_index = $key;
					break;
				}
			}
		} else {
			$remove_index = \array_search( $task_id, $option[ $status ], true );
		}

		if ( false === $remove_index ) {
			return false;
		}

		unset( $option[ $status ][ $remove_index ] );
		return \update_option( self::OPTION_NAME, $option );
	}

	/**
	 * Transition a task from one status to another.
	 *
	 * @param string $task_id The task ID.
	 * @param string $old_status The old status.
	 * @param string $new_status The new status.
	 * @param array  $data The data.
	 *
	 * @return bool
	 */
	public function transition_task_status( $task_id, $old_status, $new_status, $data = [] ) {

		$return_old_status = false;
		$return_new_status = false;

		if ( $old_status ) {
			$return_old_status = $this->remove_task_from( $old_status, $task_id );
		}

		if ( $new_status ) {
			$return_new_status = $this->mark_task_as( $new_status, $task_id, $data );
		}

		return $return_old_status && $return_new_status;
	}

	/**
	 * Get the snoozed tasks.
	 *
	 * @return array
	 */
	public function get_snoozed_tasks() {
		$option  = \get_option( self::OPTION_NAME, [] );
		$snoozed = $option['snoozed'] ?? [];

		return $snoozed;
	}

	/**
	 * Get the completed tasks.
	 *
	 * @return array
	 */
	public function get_completed_tasks() {
		$option    = \get_option( self::OPTION_NAME, [] );
		$completed = $option['completed'] ?? [];

		return $completed;
	}

	/**
	 * Mark a task as snoozed.
	 *
	 * @param string $task_id The task ID.
	 * @param string $duration The duration.
	 *
	 * @return bool
	 */
	public function snooze_task( $task_id, $duration ) {

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

		// Remove the task from the pending local tasks list.
		$this->local->remove_pending_task( $task_id );

		return $this->mark_task_as_snoozed( $task_id, $time );
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

		foreach ( $option['snoozed'] as $task ) {
			if ( $task['time'] < $current_time ) {
				$this->remove_task_from( 'snoozed', $task['id'] );
			}
		}
	}

	/**
	 * Check if a task meets a condition.
	 *
	 * @param array $condition The condition.
	 *                         [
	 *                           string  'type'         The condition type.
	 *                           string  'task_id'      The task id (optional, used for completed and snoozed conditions).
	 *                           array   'post_lengths' The post lengths (optional, used for snoozed-post-length condition).
	 *                         ].
	 *
	 * @return bool
	 */
	public function check_task_condition( $condition ) {

		if ( ! \is_array( $condition ) ) {
			$condition['type'] = $condition;
		}

		$parsed_condition = \wp_parse_args(
			$condition,
			[
				'type'         => '',
				'task_id'      => '',
				'post_lengths' => [],
			]
		);

		if ( 'completed' === $parsed_condition['type'] ) {
			$completed_tasks = $this->get_completed_tasks();

			if ( \in_array( $parsed_condition['task_id'], $completed_tasks, true ) ) {
				return true;
			}
		}

		if ( 'snoozed' === $parsed_condition['type'] ) {
			$snoozed_tasks = $this->get_snoozed_tasks();

			if ( \in_array( $parsed_condition['task_id'], $snoozed_tasks, true ) ) {
				return true;
			}
		}

		if ( 'snoozed-post-length' === $parsed_condition['type'] && isset( $parsed_condition['post_lengths'] ) ) {
			if ( ! \is_array( $parsed_condition['post_lengths'] ) ) {
				$parsed_condition['post_lengths'] = [ $parsed_condition['post_lengths'] ];
			}

			$snoozed_tasks        = $this->get_snoozed_tasks();
			$snoozed_post_lengths = [];

			// Get the post lengths of the snoozed tasks.
			foreach ( $snoozed_tasks as $task ) {
				$data = $this->local->get_data_from_task_id( $task['id'] );
				if ( isset( $data['type'] ) && 'create-post' === $data['type'] ) {
					$key = true === $data['long'] ? 'long' : 'short';
					if ( ! isset( $snoozed_post_lengths[ $key ] ) ) {
						$snoozed_post_lengths[ $key ] = true;
					}
				}
			}

			// Check if the snoozed post lengths match the condition.
			foreach ( $parsed_condition['post_lengths'] as $post_length ) {
				if ( ! isset( $snoozed_post_lengths[ $post_length ] ) ) {
					return false;
				}
			}

			return true;
		}

		// If no condition is met, return false.
		return false;
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
				$this->mark_task_as_pending_celebration( $task_id );
				$updated = true;
				break;

			case 'snooze':
				$duration = isset( $_POST['duration'] ) ? \sanitize_text_field( \wp_unslash( $_POST['duration'] ) ) : '';
				$updated  = $this->snooze_task( $task_id, $duration );
				break;

			case 'celebrated':
				$this->transition_task_status( $task_id, 'pending_celebration', 'completed' );
				$updated = true;
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
