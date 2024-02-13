<?php
/**
 * Stats about posts.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

use ProgressPlanner\Chart;

/**
 * Stats about posts.
 */
class Stat_Posts extends Stat {

	/**
	 * The post-type for this stat.
	 *
	 * @var string
	 */
	protected $post_type = 'post';

	/**
	 * The stat type. This is used as a key in the settings array.
	 *
	 * @var string
	 */
	protected $type = 'posts';

	/**
	 * Set the post-type for this stat.
	 *
	 * @param string $post_type The post-type.
	 *
	 * @return Stat_Posts Returns this object to allow chaining methods.
	 */
	public function set_post_type( $post_type ) {
		$this->post_type = $post_type;
		return $this;
	}

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
	 * Build a chart for the stats.
	 *
	 * @param array  $post_types The post types.
	 * @param string $context    The context for the chart. Can be 'count' or 'words'.
	 * @param string $interval   The interval for the chart. Can be 'days', 'weeks', 'months', 'years'.
	 * @param int    $range      The number of intervals to show.
	 * @param int    $offset     The offset for the intervals.
	 */
	public function build_chart( $post_types = [], $context = 'count', $interval = 'weeks', $range = 10, $offset = 0 ) {
		$post_types = empty( $post_types )
			? $this->get_post_types_names()
			: $post_types;

		$range_array_end   = \range( $offset, $range - 1 );
		$range_array_start = \range( $offset + 1, $range );
		\krsort( $range_array_start );
		\krsort( $range_array_end );

		$range_array = \array_combine( $range_array_start, $range_array_end );

		$data                   = [
			'labels'   => [],
			'datasets' => [],
		];
		$datasets               = [];
		$post_type_count_totals = [];
		foreach ( $post_types as $post_type ) {
			$post_type_count_totals[ $post_type ] = 0;
			$datasets[ $post_type ]               = [
				'label' => \get_post_type_object( $post_type )->label,
				'data'  => [],
			];
		}

		foreach ( $range_array as $start => $end ) {
			$stats = $this->get_stats( "-$start $interval", "-$end $interval", $post_types );

			// TODO: Format the date depending on the user's locale.
			$data['labels'][] = gmdate( 'Y-m-d', strtotime( "-$start $interval" ) );

			foreach ( $post_types as $post_type ) {
				foreach ( $stats as $posts ) {
					foreach ( $posts as $post_details ) {
						if ( $post_details['post_type'] === $post_type ) {
							if ( 'words' === $context ) {
								$post_type_count_totals[ $post_type ] += $post_details['words'];
								continue;
							}
							++$post_type_count_totals[ $post_type ];
						}
					}
				}
				$datasets[ $post_type ]['data'][] = $post_type_count_totals[ $post_type ];
			}
		}
		$data['datasets'] = \array_values( $datasets );

		$chart = new Chart();
		$chart->render_chart(
			md5( wp_json_encode( [ $post_types, $context, $interval, $range, $offset ] ) ),
			'line',
			$data,
			[]
		);
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
