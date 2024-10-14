<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Widgets\Widget;

/**
 * Whats new widget.
 */
final class Whats_New extends Widget {

	/**
	 * The remote server ROOT URL.
	 *
	 * @var string
	 */
	const REMOTE_SERVER_ROOT_URL = 'https://progressplanner.com';

	/**
	 * The transient name.
	 *
	 * @var string
	 */
	const TRANSIENT_NAME = 'progress_planner_blog_feed_with_images';

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'whats-new';

	/**
	 * Render the widget content.
	 */
	public function the_content() {
		/**
		 * Filters the template to use for the widget.
		 *
		 * @param string $template The template to use.
		 * @param string $id       The widget ID.
		 *
		 * @return string The template to use.
		 */
		include \apply_filters(
			'progress_planner_widgets_template',
			PROGRESS_PLANNER_DIR . '/views/widgets/whats-new.php',
			$this->id
		);
	}

	/**
	 * Get the feed from the blog.
	 *
	 * @return array
	 */
	public function get_blog_feed() {
		$feed_data = \get_site_transient( self::TRANSIENT_NAME );

		// Migrate old feed to new format.
		if ( is_array( $feed_data ) && ! isset( $feed_data['expires'] ) && ! isset( $feed_data['feed'] ) ) {
			$feed_data = [
				'feed'    => $feed_data,
				'expires' => get_option( '_site_transient_timeout_' . self::TRANSIENT_NAME, 0 ),
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
			$response = \wp_remote_get( self::REMOTE_SERVER_ROOT_URL . '/wp-json/wp/v2/posts/?per_page=2' );

			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				// If we cant fetch the feed, we will try again later.
				$feed_data['expires'] = time() + 5 * MINUTE_IN_SECONDS;
			} else {
				$feed = json_decode( \wp_remote_retrieve_body( $response ), true );

				foreach ( $feed as $key => $post ) {
					// Get the featured media.
					$featured_media_id = $post['featured_media'];
					if ( $featured_media_id ) {
						$response = \wp_remote_get( self::REMOTE_SERVER_ROOT_URL . '/wp-json/wp/v2/media/' . $featured_media_id );
						if ( ! \is_wp_error( $response ) ) {
							$media = json_decode( \wp_remote_retrieve_body( $response ), true );

							$post['featured_media'] = $media;
						}
					}
					$feed[ $key ] = $post;
				}

				$feed_data['feed']    = $feed;
				$feed_data['expires'] = time() + 1 * DAY_IN_SECONDS;
			}

			// Transient uses 'expires' key to determine if it's expired.
			\set_site_transient( self::TRANSIENT_NAME, $feed_data, 0 );
		}

		return $feed_data['feed'];
	}
}
