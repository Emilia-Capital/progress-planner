<?php
/**
 * Posts chart.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Charts;

use ProgressPlanner\Chart;
use ProgressPlanner\Stats\Stat_Posts;

/**
 * Posts chart.
 */
class Posts extends Chart {

	/**
	 * Build a chart for the stats.
	 *
	 * @param array  $post_types The post types.
	 * @param string $context    The context for the chart. Can be 'count' or 'words'.
	 * @param string $interval   The interval for the chart. Can be 'days', 'weeks', 'months', 'years'.
	 * @param int    $range      The number of intervals to show.
	 * @param int    $offset     The offset for the intervals.
	 */
	public function render( $post_types = [], $context = 'count', $interval = 'weeks', $range = 10, $offset = 0 ) {
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

		$stat_posts = new Stat_Posts();
		foreach ( $range_array as $start => $end ) {
			$stats = $stat_posts->get_stats(
				(int) gmdate( 'Ymd', strtotime( "-$start $interval" ) ),
				(int) gmdate( 'Ymd', strtotime( "-$end $interval" ) ),
				$post_types
			);

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

		$this->render_chart(
			md5( wp_json_encode( [ $post_types, $context, $interval, $range, $offset ] ) ),
			'line',
			$data,
			[]
		);
	}
}
