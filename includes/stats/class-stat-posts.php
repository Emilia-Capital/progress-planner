<?php
/**
 * Stats about posts.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

use ProgressPlanner\Date;

/**
 * Stats about posts.
 */
class Stat_Posts {

	/**
	 * The setting name.
	 *
	 * @var string
	 */
	const SETTING_NAME = 'progress_planner_stats_posts';

	/**
	 * The number of posts to scan at a time.
	 *
	 * @var int
	 */
	const SCAN_POSTS_PER_PAGE = 20;

	/**
	 * The option used to store the last scanned page.
	 *
	 * @var string
	 */
	const LAST_SCANNED_PAGE_OPTION = 'progress_planner_stats_last_scanned_page';

	/**
	 * The stats. Used for caching purposes.
	 *
	 * @var array
	 */
	private static $stats;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		\add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
	}

	/**
	 * Run actions when saving a post.
	 *
	 * @param int      $post_id The post ID.
	 * @param \WP_Post $post    The post object.
	 */
	public function save_post( $post_id, $post ) {
		// Bail if the post is not included in the post-types we're tracking.
		$post_types = $this->get_post_types_names();
		if ( ! \in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		// Bail if the post is not published.
		if ( 'publish' !== $post->post_status ) {
			return;
		}

		$this->save_post_stats( $post );
	}

	/**
	 * Get the value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		if ( ! self::$stats ) {
			$value = \get_option( static::SETTING_NAME, [] );
			ksort( $value );
			self::$stats = $value;
		}
		return self::$stats;
	}

	/**
	 * Save a post to the stats.
	 *
	 * @param \WP_Post $post The post.
	 *
	 * @return bool
	 */
	protected function save_post_stats( $post ) {
		$value = \get_option( static::SETTING_NAME, [] );
		$date  = (int) mysql2date( Date::FORMAT, $post->post_date );

		// Remove the post from stats if it's already stored in another date.
		foreach ( $value as $date_key => $date_value ) {
			if ( isset( $date_value[ $post->ID ] ) ) {
				unset( $value[ $date_key ][ $post->ID ] );
			}
		}

		if ( ! isset( $value[ $date ] ) ) {
			$value[ $date ] = [];
		}
		$value[ $date ][ $post->ID ] = [
			'post_type' => $post->post_type,
			'words'     => $this->get_word_count( $post->post_content ),
		];
		return \update_option( static::SETTING_NAME, $value );
	}

	/**
	 * Get stats for date range.
	 *
	 * @param int|string $start_date The start date.
	 * @param int|string $end_date   The end date.
	 * @param string[]   $post_types The post types.
	 *
	 * @return array
	 */
	public function get_stats( $start_date, $end_date, $post_types = [] ) {
		$stats = $this->get_value();

		// Get the stats for the date range and post types.
		foreach ( $stats as $date => $date_stats ) {
			// Remove stats outside the date range.
			if ( $date <= $start_date || $date > $end_date ) {
				unset( $stats[ $date ] );
				continue;
			}

			// If we have not defined post types, then we don't need to filter by post type.
			if ( empty( $post_types ) ) {
				continue;
			}

			// Remove stats not in the post types.
			foreach ( $stats[ $date ] as $post_id => $details ) {
				if ( ! \in_array( $details['post_type'], $post_types, true ) ) {
					unset( $stats[ $date ][ $post_id ] );
				}
			}

			// Remove empty dates.
			if ( ! $stats[ $date ] || empty( $stats[ $date ] ) ) {
				unset( $stats[ $date ] );
				continue;
			}
		}

		return $stats;
	}

	/**
	 * Get an array of post-types names for the stats.
	 *
	 * @return string[]
	 */
	public function get_post_types_names() {
		$post_types = \get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );

		return array_keys( $post_types );
	}

	/**
	 * Get words count from content.
	 *
	 * This method will render shortcodes, blocks,
	 * and strip HTML before counting the words.
	 *
	 * @param string $content The content.
	 *
	 * @return int
	 */
	protected function get_word_count( $content ) {
		// Parse blocks and shortcodes.
		$content = \do_blocks( \do_shortcode( $content ) );

		// Strip HTML.
		$content = \wp_strip_all_tags( $content, true );

		// Count words.
		return \str_word_count( $content );
	}

	/**
	 * Update stats for posts.
	 * - Gets the next page to scan.
	 * - Gets the posts for the page.
	 * - Updates the stats for the posts.
	 * - Updates the last scanned page option.
	 *
	 * @return array
	 */
	public function update_stats() {

		// Get the total number of posts.
		$total_posts_count = 0;
		foreach ( $this->get_post_types_names() as $post_type ) {
			$total_posts_count += \wp_count_posts( $post_type )->publish;
		}
		// Calculate the total pages to scan.
		$total_pages = \ceil( $total_posts_count / static::SCAN_POSTS_PER_PAGE );
		// Get the last scanned page.
		$last_page = (int) \get_option( static::LAST_SCANNED_PAGE_OPTION, 0 );
		// The current page to scan.
		$current_page = $last_page + 1;

		// Get posts.
		$posts = \get_posts(
			[
				'posts_per_page' => static::SCAN_POSTS_PER_PAGE,
				'paged'          => $current_page,
				'post_type'      => $this->get_post_types_names(),
				'post_status'    => 'publish',
			]
		);

		if ( $posts ) {
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

		if ( $current_page > $total_pages ) {
			\delete_option( static::LAST_SCANNED_PAGE_OPTION );
		}
		\update_option( static::LAST_SCANNED_PAGE_OPTION, $current_page );

		return [
			'lastScannedPage' => $last_page,
			'lastPage'        => $total_pages,
			'progress'        => round( ( $current_page / max( 1, $total_pages ) ) * 100 ),
		];
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
