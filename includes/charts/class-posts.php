<?php
/**
 * Posts chart.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Charts;

use ProgressPlanner\Chart;
use ProgressPlanner\Date;
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
	 *
	 * @return void
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
				'fill'  => true,
			];
		}

		$stat_posts = new Stat_Posts();

		// Calculate zero stats to be used as the baseline.
		$zero_stats = $stat_posts->get_stats(
			19700101,
			(int) gmdate( Date::FORMAT, strtotime( "-$range $interval" ) ),
			$post_types
		);
		foreach ( $zero_stats as $zero_posts ) {
			foreach ( $zero_posts as $zero_post ) {
				$post_type_count_totals[ $zero_post['post_type'] ] += 'words' === $context
					? $zero_post['words']
					: 1;
			}
		}

		foreach ( $range_array as $start => $end ) {
			$stats = $stat_posts->get_stats(
				(int) gmdate( Date::FORMAT, strtotime( "-$start $interval" ) ),
				(int) gmdate( Date::FORMAT, strtotime( "-$end $interval" ) ),
				$post_types
			);

			// TODO: Format the date depending on the user's locale.
			$data['labels'][] = gmdate( 'Y-m-d', strtotime( "-$start $interval" ) );

			foreach ( $post_types as $post_type ) {
				foreach ( $stats as $posts ) {
					foreach ( $posts as $post_details ) {
						if ( $post_details['post_type'] === $post_type ) {
							$post_type_count_totals[ $post_type ] += 'words' === $context
								? $post_details['words']
								: 1;
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
