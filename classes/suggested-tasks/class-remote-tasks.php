<?php
/**
 * API for suggested tasks.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

/**
 * Remote tasks class.
 */
class Remote_Tasks {

	/**
	 * The remote server URL.
	 *
	 * @var string
	 */
	const REMOTE_SERVER_ROOT_URL = 'https://progressplanner.com';

	/**
	 * The cache key to use for remote-API tasks.
	 *
	 * @var string
	 */
	const CACHE_KEY = 'suggested_tasks_remote';

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'progress_planner_suggested_tasks_items', [ $this, 'inject_tasks' ] );
	}

	/**
	 * Inject tasks.
	 *
	 * @param array $tasks The tasks.
	 *
	 * @return array
	 */
	public function inject_tasks( $tasks ) {
		$inject_items = $this->get_tasks_to_inject();
		if ( ! is_array( $inject_items ) ) {
			$inject_items = [];
		}

		return \array_merge( $inject_items, $tasks );
	}

	/**
	 * Get the tasks from the remote API.
	 *
	 * @return array
	 */
	protected function get_tasks_to_inject() {
		// Check if we have a cached response.
		$tasks = \progress_planner()->get_cache()->get( self::CACHE_KEY );

		// If we have a cached response, return it.
		if ( \is_array( $tasks ) ) {
			return $tasks;
		}

		// Get the response from the remote server.
		$response = \wp_remote_get( $this->get_api_endpoint() );

		// Bail if the request failed.
		if ( ! \is_wp_error( $response ) ) {
			// Get the body of the response.
			$body = \wp_remote_retrieve_body( $response );

			if ( ! empty( $body ) ) {
				// Decode the JSON body.
				$tasks = \json_decode( $body, true );

				if ( \is_array( $tasks ) ) {
					// Cache the response for 1 day.
					\progress_planner()->get_cache()->set( self::CACHE_KEY, $tasks, DAY_IN_SECONDS );
					return $tasks;
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
		$url             = self::REMOTE_SERVER_ROOT_URL . '/wp-json/progress-planner-saas/v1/suggested-todo/';
		$pro_license_key = \get_option( 'progress_planner_pro_license_key' );
		if ( $pro_license_key ) {
			$url = \add_query_arg(
				[
					'license_key' => $pro_license_key,
					'site'        => \get_site_url(),
				],
				$url
			);
		}
		return \apply_filters( 'progress_planner_suggested_tasks_remote_api_endpoint', $url );
	}
}
