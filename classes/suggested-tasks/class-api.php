<?php
/**
 * API for suggested tasks.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

/**
 * API class.
 */
class API {

	/**
	 * The remote server URL.
	 *
	 * @var string
	 */
	const REMOTE_DOMAIN = 'https://progressplanner.com';

	/**
	 * The cache key to use for remote-API tasks.
	 *
	 * @var string
	 */
	const CACHE_KEY = 'suggested_tasks_remote';

	/**
	 * Return filtered items.
	 *
	 * @return array
	 */
	public function get_tasks() {
		$tasks = $this->get_api_tasks();
		/**
		 * Filter the suggested tasks.
		 *
		 * @param array $tasks The suggested tasks.
		 * @return array
		 */
		return \apply_filters( 'progress_planner_suggested_tasks_api_items', $tasks );
	}

	/**
	 * Get the tasks from the remote API.
	 *
	 * @return array
	 */
	protected function get_api_tasks() {
		// Check if we have a cached response.
		$items = \progress_planner()->get_cache()->get( self::CACHE_KEY );

		// If we have a cached response, return it.
		if ( $items ) {
			return $items;
		}

		// Get the response from the remote server.
		$response = \wp_remote_get( $this->get_api_endpoint() );

		// Bail if the request failed.
		if ( ! \is_wp_error( $response ) ) {
			// Get the body of the response.
			$body = \wp_remote_retrieve_body( $response );

			if ( ! empty( $body ) ) {
				// Decode the JSON body.
				$data = \json_decode( $body, true );

				if ( \is_array( $data ) ) {
					// Cache the response for 1 day.
					\progress_planner()->get_cache()->set( self::CACHE_KEY, $data, DAY_IN_SECONDS );
					return $data;
				}
			}
		}
		return [];
	}

	/**
	 * Get the remote API endpoint.
	 *
	 * @return string
	 */
	protected function get_api_endpoint() {
		return self::REMOTE_DOMAIN . '/wp-json/progress-planner-saas/v1/suggested-todo/';
	}

	/**
	 * Get an array of completed and snoozed tasks.
	 *
	 * @return array
	 */
	public function get_saved_tasks() {
		$option              = \get_option( \Progress_Planner\Suggested_Tasks::OPTION_NAME, [] );
		$option['completed'] = $option['completed'] ?? [];
		$option['snoozed']   = $option['snoozed'] ?? [];

		// Convert the task IDs to strings.
		$option['completed'] = \array_map( 'strval', $option['completed'] );
		$option['snoozed']   = \array_map(
			function ( $task ) {
				return [
					'id'   => (string) $task['id'],
					'time' => (int) $task['time'],
				];
			},
			$option['snoozed']
		);

		// Remove items with id 0.
		$option['completed'] = \array_values( \array_filter( $option['completed'] ) );
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
