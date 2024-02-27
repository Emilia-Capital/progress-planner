<?php
/**
 * Posts chart.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Charts;

use ProgressPlanner\Chart;
use ProgressPlanner\Activities\Query;

/**
 * Posts chart.
 */
class Posts extends Chart {

	/**
	 * Build a chart for the stats.
	 *
	 * @param string $post_type  The post type.
	 * @param string $context    The context for the chart. Can be 'count' or 'words'.
	 * @param string $interval   The interval for the chart. Can be 'days', 'weeks', 'months', 'years'.
	 * @param int    $range      The number of intervals to show.
	 * @param int    $offset     The offset for the intervals.
	 * @param string $date_format The date format.
	 *
	 * @return void
	 */
	public function render( $post_type = 'post', $context = 'count', $interval = 'weeks', $range = 10, $offset = 0, $date_format = 'Y-m-d' ) {
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
		$post_type_count_totals = 0;
		$dataset                = [
			'label'   => \get_post_type_object( $post_type )->label,
			'data'    => [],
			'tension' => 0.2,
		];

		// Calculate zero stats to be used as the baseline.
		$zero_activities = Query::get_instance()->query_activities(
			[
				'category'   => 'post',
				'type'       => 'publish',
				'start_date' => \DateTime::createFromFormat( $date_format, '1970-01-01' ),
				'end_date'   => new \DateTime( "-$range $interval" ),
				'data'       => [
					'post_type' => $post_type,
				],
			]
		);
		foreach ( $zero_activities as $zero_activity ) {
			$activity_data           = $zero_activity->get_data();
			$post_type_count_totals += 'words' === $context
				? $activity_data['word_count']
				: 1;
		}

		foreach ( $range_array as $start => $end ) {
			$activities = Query::get_instance()->query_activities(
				[
					'category'   => 'post',
					'type'       => 'publish',
					'start_date' => new \DateTime( "-$start $interval" ),
					'end_date'   => new \DateTime( "-$end $interval" ),
					'data'       => [
						'post_type' => $post_type,
					],
				]
			);

			// TODO: Format the date depending on the user's locale.
			$data['labels'][] = gmdate( $date_format, strtotime( "-$start $interval" ) );

			foreach ( $activities as $activity ) {
				$activity_data           = $activity->get_data();
				$post_type_count_totals += 'words' === $context
					? $activity_data['word_count']
					: 1;
			}
			$datasets[ $post_type ]['data'][] = $post_type_count_totals;
		}
		$data['datasets'] = \array_values( $datasets );

		$this->render_chart(
			md5( wp_json_encode( [ [ $post_type ], $context, $interval, $range, $offset ] ) ),
			'line',
			$data,
			[]
		);
	}
}
