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
	 */
	const SETTING_NAME = 'progress_planner_stats_posts';

	/**
	 * Get the value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		$value = \get_option( static::SETTING_NAME, [] );
		ksort( $value );
		return $value;
	}

	/**
	 * Save a post to the stats.
	 *
	 * @param \WP_Post $post The post.
	 *
	 * @return bool
	 */
	protected function save_post( $post ) {
		$value = \get_option( static::SETTING_NAME, [] );
		$date  = (int) mysql2date( Date::FORMAT, $post->post_date );

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
		foreach ( array_keys( $stats ) as $key ) {
			// Remove the stats that are outside the date range.
			if ( $key <= $start_date || $key > $end_date ) {
				unset( $stats[ $key ] );
				continue;
			}

			// If we have not defined post types, then we don't need to filter by post type.
			if ( empty( $post_types ) ) {
				continue;
			}

			// Remove the stats that are not in the post types.
			foreach ( $stats[ $key ] as $post_id => $details ) {
				if ( ! \in_array( $details['post_type'], $post_types, true ) ) {
					unset( $stats[ $key ][ $post_id ] );
				}
			}
		}

		// Filter out empty dates.
		$stats = \array_filter( $stats );

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
}
