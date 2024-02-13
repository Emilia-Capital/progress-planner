<?php
/**
 * Stats about posts.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

use ProgressPlanner\Charts\Posts as Posts_Chart;

/**
 * Stats about posts.
 */
class Stat_Posts extends Stat {

	/**
	 * The stat type. This is used as a key in the settings array.
	 *
	 * @var string
	 */
	protected $type = 'posts';

	/**
	 * Save a post to the stats.
	 *
	 * @param \WP_Post $post The post.
	 *
	 * @return void
	 */
	protected function save_post( $post ) {
		// error_log( $post->post_date . ' => ' . mysql2date( 'Ymd', $post->post_date ) );
		// Get the date.
		$date = (int) mysql2date( 'Ymd', $post->post_date );

		// Add the post to the stats.
		$this->set_value(
			[ $date, $post->ID ],
			[
				'post_type' => $post->post_type,
				'words'     => \str_word_count( $post->post_content ),
			],
		);
	}

	/**
	 * Get stats for date range.
	 *
	 * @param string $start_date The start date.
	 * @param string $end_date   The end date.
	 * @param array  $post_types The post types.
	 *
	 * @return array
	 */
	public function get_stats( $start_date, $end_date, $post_types = [] ) {
		$stats = $this->get_value();

		// Format the start and end dates.
		$start_date = (int) gmdate( 'Ymd', strtotime( $start_date ) );
		$end_date   = (int) gmdate( 'Ymd', strtotime( $end_date ) );

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
	 * Reset the stats in our database.
	 *
	 * @return void
	 */
	public function reset_stats() {
		$this->set_value( [], [] );
	}

	/**
	 * Get an array of post-types names for the stats.
	 *
	 * @return array
	 */
	public function get_post_types_names() {
		$post_types = \get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );

		return array_keys( $post_types );
	}
}
