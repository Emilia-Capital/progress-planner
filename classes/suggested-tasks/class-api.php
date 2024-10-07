<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

use Progress_Planner\Suggested_Tasks;

/**
 * Settings class.
 */
class API {

	/**
	 * The remote server URL.
	 *
	 * @var string
	 */
	const REMOTE_DOMAIN = 'https://progressplanner.com';

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
		$option              = \get_option( Suggested_Tasks::OPTION_NAME, [] );
		$option['completed'] = $option['completed'] ?? [];
		$option['dismissed'] = $option['dismissed'] ?? [];
		$option['snoozed']   = $option['snoozed'] ?? [];

		// Convert the task IDs to integers.
		$option['completed'] = \array_map( 'intval', $option['completed'] );
		$option['dismissed'] = \array_map( 'intval', $option['dismissed'] );
		$option['snoozed']   = \array_map(
			function ( $task ) {
				return [
					'id'   => (int) $task['id'],
					'time' => (int) $task['time'],
				];
			},
			$option['snoozed']
		);

		// Remove items with id 0.
		$option['completed'] = \array_values( \array_filter( $option['completed'] ) );
		$option['dismissed'] = \array_values( \array_filter( $option['dismissed'] ) );
		$option['snoozed']   = \array_values(
			\array_filter(
				$option['snoozed'],
				function ( $task ) {
					return $task['id'] > 0;
				}
			)
		);
		return $option;
	}
}
