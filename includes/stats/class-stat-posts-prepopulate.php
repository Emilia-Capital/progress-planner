<?php
/**
 * Prepopulate the posts stats.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

use ProgressPlanner\Date;

/**
 * Prepopulate the posts stats.
 */
class Stat_Posts_Prepopulate extends Stat_Posts {

	/**
	 * The number of posts to prepopulate at a time.
	 *
	 * @var int
	 */
	const POSTS_PER_PAGE = 20;

	/**
	 * The option used to store the last scanned page.
	 *
	 * @var string
	 */
	const LAST_SCANNED_PAGE_OPTION = 'progress_planner_stats_last_scanned_page';

	/**
	 * The total posts count.
	 *
	 * @var int
	 */
	private static $posts_count = 0;

	/**
	 * Update stats for posts.
	 * - Gets the next page to scan.
	 * - Gets the posts for the page.
	 * - Updates the stats for the posts.
	 * - Updates the last scanned page option.
	 *
	 * @return void
	 */
	public function update_stats() {
		$last_page = $this->get_last_scanned_page();
		$next_page = $last_page + 1;
		$this->update_stats_for_posts( $next_page );
		$this->update_last_scanned_page( $next_page );
	}

	/**
	 * Update stats for posts.
	 *
	 * @param int $page The page to query.
	 */
	private function update_stats_for_posts( $page ) {
		$posts = \get_posts(
			[
				'posts_per_page' => static::POSTS_PER_PAGE,
				'paged'          => $page,
				'post_type'      => $this->get_post_types_names(),
				'post_status'    => 'publish',
			]
		);

		if ( ! $posts ) {
			return;
		}

		// Get the value from the option.
		$value = \get_option( static::SETTING_NAME, [] );

		// Loop through the posts and update the $value stats.
		foreach ( $posts as $post ) {
			// Get the date from the post, and convert it to the format we use.
			$date = (int) mysql2date( Date::FORMAT, $post->post_date );

			// If the date is not set in the option, set it to an empty array.
			if ( ! isset( $value[ $date ] ) ) {
				$value[ $date ] = [];
			}

			// Add the post to the date.
			$value[ $date ][ $post->ID ] = [
				'post_type' => $post->post_type,
				'words'     => $this->get_word_count( $post->post_content ),
			];
		}

		// Save the option.
		\update_option( static::SETTING_NAME, $value );
	}

	/**
	 * Get the total posts count.
	 *
	 * @return int
	 */
	private function get_posts_count() {
		if ( static::$posts_count ) {
			return static::$posts_count;
		}
		foreach ( $this->get_post_types_names() as $post_type ) {
			static::$posts_count += \wp_count_posts( $post_type )->publish;
		}
		return static::$posts_count;
	}

	/**
	 * Get number of pages to scan.
	 *
	 * @return int
	 */
	public function get_total_pages_to_scan() {
		return \ceil( $this->get_posts_count() / static::POSTS_PER_PAGE );
	}

	/**
	 * Get last scanned page.
	 *
	 * @return int
	 */
	public function get_last_scanned_page() {
		return (int) \get_option( static::LAST_SCANNED_PAGE_OPTION, 1 );
	}

	/**
	 * Update last scanned page.
	 *
	 * @param int $page The page to set.
	 */
	private function update_last_scanned_page( $page ) {
		if ( $page > $this->get_total_pages_to_scan() ) {
			\delete_option( static::LAST_SCANNED_PAGE_OPTION );
			return;
		}
		\update_option( static::LAST_SCANNED_PAGE_OPTION, $page );
	}

	/**
	 * Reset the stats in our database.
	 *
	 * @return void
	 */
	public function reset_stats() {
		\delete_option( static::SETTING_NAME );
		\delete_option( static::LAST_SCANNED_PAGE_OPTION );
	}
}
