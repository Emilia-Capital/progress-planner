<?php
/**
 * Lessons class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

/**
 * Get the lessons from a remote server.
 *
 * @package Progress_Planner
 */
class Lessons {

	/**
	 * The ID for this API object.
	 *
	 * @var string
	 */
	protected $id = 'lessons';

	/**
	 * Get items from the remote API.
	 *
	 * @return array
	 */
	public function get_remote_api_items() {
		$cached = \get_site_transient( 'progress_planner_lessons' );
		if ( is_array( $cached ) && ! empty( $cached ) ) {
			return $cached;
		}

		$response = \wp_remote_get( 'https://progressplanner.com/wp-json/progress-planner-saas/v1/free-lessons' );

		if ( \is_wp_error( $response ) ) {
			return [];
		}

		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return [];
		}

		$json = json_decode( \wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $json ) ) {
			return [];
		}

		\set_site_transient( 'progress_planner_lessons', $json, WEEK_IN_SECONDS );

		return $json;
	}

	/**
	 * Get the lessons pagetypes.
	 *
	 * @return array
	 */
	public function get_lesson_pagetypes() {
		$lessons       = $this->get_remote_api_items();
		$pagetypes     = [];
		$show_on_front = \get_option( 'show_on_front' );

		foreach ( $lessons as $lesson ) {
			// Remove the "homepage" lesson if the site doesn't show a static page as the frontpage.
			if ( 'posts' === $show_on_front && 'homepage' === $lesson['settings']['id'] ) {
				continue;
			}
			$pagetypes[] = [
				'label' => $lesson['name'],
				'value' => $lesson['settings']['id'],
			];
		}
		return $pagetypes;
	}
}
