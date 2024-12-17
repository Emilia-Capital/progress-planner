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
	 * Get the current challenge.
	 *
	 * @return array
	 */
	public function get_current_challenge() {
		$challenges = $this->get_challenges();
		$now        = new \DateTime();

		foreach ( $challenges as $challenge ) {
			$start_date = \DateTime::createFromFormat( 'd/m/Y', $challenge['start_date'] );
			$end_date   = \DateTime::createFromFormat( 'd/m/Y', $challenge['end_date'] );

			if ( $start_date <= $now && $end_date >= $now ) {
				return $challenge;
			}
		}

		return [];
	}

	/**
	 * Get the feed from the blog.
	 *
	 * @return array
	 */
	public function get_challenges() {
		$cache_key = $this->get_cache_key();
		$feed_data = \progress_planner()->get_cache()->get( $cache_key );

		// Migrate old feed to new format.
		if ( is_array( $feed_data ) && ! isset( $feed_data['expires'] ) && ! isset( $feed_data['feed'] ) ) {
			$feed_data = [
				'feed'    => $feed_data,
				'expires' => get_option( '_transient_timeout_' . \Progress_Planner\Cache::CACHE_PREFIX . $cache_key, 0 ),
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
			$response = \wp_remote_get( \progress_planner()->get_remote_server_root_url() . '/wp-json/progress-planner-saas/v1/challenges' );

			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				// If we cant fetch the feed, we will try again later.
				$feed_data['expires'] = time() + 5 * MINUTE_IN_SECONDS;
			} else {
				$feed = json_decode( \wp_remote_retrieve_body( $response ), true );

				$feed_data['feed']    = $feed;
				$feed_data['expires'] = time() + 1 * DAY_IN_SECONDS;
			}

			// Transient uses 'expires' key to determine if it's expired.
			\progress_planner()->get_cache()->set( $cache_key, $feed_data, 0 );
		}

		return $feed_data['feed'];
	}

	/**
	 * Render the widget.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! \progress_planner()->is_pro_site() || empty( $this->get_challenges() ) ) {
			return;
		}
		parent::render();
	}

	/**
	 * Get the cache key.
	 *
	 * @return string
	 */
	public function get_cache_key() {
		return md5( $this->get_remote_api_url() );
	}

	/**
	 * Get the remote-API URL.
	 *
	 * @return string
	 */
	public function get_remote_api_url() {
		$url = \progress_planner()->get_remote_server_root_url() . '/wp-json/progress-planner-saas/v1/challenges';
		if ( \progress_planner()->is_pro_site() ) {
			$url = \add_query_arg(
				[
					'license_key' => \get_option( 'progress_planner_pro_license_key' ),
					'site'        => \get_site_url(),
				],
				$url
			);
		}
		return $url;
	}
}
