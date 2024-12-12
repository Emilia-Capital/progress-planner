<?php
/**
 * A widget class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * Challenges class.
 */
final class Challenges extends \Progress_Planner\Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'challenges';

	/**
	 * The remote server ROOT URL.
	 *
	 * @var string
	 */
	const REMOTE_SERVER_ROOT_URL = 'https://progressplanner.com';

	/**
	 * The cache key.
	 *
	 * @var string
	 */
	const CACHE_KEY = 'challenges';

	/**
	 * Get the feed from the blog.
	 *
	 * @return array
	 */
	public function get_challenges() {
		$feed_data = \progress_planner()->get_cache()->get( self::CACHE_KEY );

		// Migrate old feed to new format.
		if ( is_array( $feed_data ) && ! isset( $feed_data['expires'] ) && ! isset( $feed_data['feed'] ) ) {
			$feed_data = [
				'feed'    => $feed_data,
				'expires' => get_option( '_transient_timeout_' . \Progress_Planner\Cache::CACHE_PREFIX . self::CACHE_KEY, 0 ),
			];
		}

		// Transient not set.
		if ( false === $feed_data ) {
			$feed_data = [
				'feed'    => [],
				'expires' => 0,
			];
		}

		// Transient expired, fetch new feed.
		if ( $feed_data['expires'] < time() ) {
			// Get the feed using the REST API.
			$response = \wp_remote_get( self::REMOTE_SERVER_ROOT_URL . '/wp-json/progress-planner-saas/v1/challenges' );

			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				// If we cant fetch the feed, we will try again later.
				$feed_data['expires'] = time() + 5 * MINUTE_IN_SECONDS;
			} else {
				$feed = json_decode( \wp_remote_retrieve_body( $response ), true );

				$feed_data['feed']    = $feed;
				$feed_data['expires'] = time() + 1 * DAY_IN_SECONDS;
			}

			// Transient uses 'expires' key to determine if it's expired.
			\progress_planner()->get_cache()->set( self::CACHE_KEY, $feed_data, 0 );
		}

		return $feed_data['feed'];
	}

	/**
	 * Render the widget.
	 *
	 * @return void
	 */
	public function render() {
		if ( empty( $this->get_challenges() ) ) {
			return;
		}
		parent::render();
	}
}
