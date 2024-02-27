<?php
/**
 * Generate charts for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Activities\Query;

/**
 * Render a chart.
 */
class Chart {

	/**
	 * Build a chart for the stats.
	 *
	 * @param array $query_params             The query parameters.
	 *       string $query_params['category'] The category for the query.
	 *       string $query_params['type']     The type for the query.
	 *       array  $query_params['data']     The data for the query.
	 * @param array $dates_params             The dates parameters for the query.
	 *       string $dates_params['start']    The start date for the query.
	 *       string $dates_params['end']      The end date for the query.
	 *       string $dates_params['interval'] The interval for the query.
	 *       string $dates_params['format']   The format for the query.
	 *       int    $dates_params['range']    The range for the query.
	 * @param array $chart_params             The chart parameters.
	 *
	 * @return void
	 */
	public function the_chart( $query_params = [], $dates_params = [], $chart_params = [] ) {
		$chart_params = wp_parse_args(
			$chart_params,
			[
				'type'    => 'line',
				'options' => [
					'pointStyle' => false,
					'plugins'    => [
						'legend' => [
							'display' => false,
						],
					],
				],
			]
		);

		$range_array_end   = \range( 0, $dates_params['range'] - 1 );
		$range_array_start = \range( 1, $dates_params['range'] );
		\krsort( $range_array_start );
		\krsort( $range_array_end );

		$range_array = \array_combine( $range_array_start, $range_array_end );

		$data     = [
			'labels'   => [],
			'datasets' => [],
		];
		$datasets = [
			[
				'label'   => '',
				'data'    => [],
				'tension' => 0.2,
			],
		];

		// Calculate zero stats to be used as the baseline.
		$zero_activities = Query::get_instance()->query_activities(
			array_merge(
				$query_params,
				[
					'start_date' => \DateTime::createFromFormat( 'Y-m-d', '1970-01-01' ),
					'end_date'   => new \DateTime( "-{$dates_params['range']} {$dates_params['interval']}" ),
				]
			)
		);
		$activities_count = count( $zero_activities );

		foreach ( $range_array as $start => $end ) {
			$activities = Query::get_instance()->query_activities(
				array_merge(
					$query_params,
					[
						'start_date' => new \DateTime(
							"-$start {$dates_params['interval']}"
						),
						'end_date'   => new \DateTime(
							"-$end {$dates_params['interval']}"
						),
					]
				)
			);

			// TODO: Format the date depending on the user's locale.
			$data['labels'][] = gmdate(
				$dates_params['format'],
				strtotime( "-$start {$dates_params['interval']}" )
			);

			$activities_count     += count( $activities );
			$datasets[0]['data'][] = $activities_count;
		}
		$data['datasets'] = $datasets;

		$this->render_chart(
			md5( wp_json_encode( [ $query_params, $dates_params ] ) ),
			$chart_params['type'],
			$data,
			$chart_params['options']
		);
	}

	/**
	 * Render the chart.
	 *
	 * @param string $id      The ID of the chart.
	 * @param string $type    The type of chart.
	 * @param array  $data    The data for the chart.
	 * @param array  $options The options for the chart.
	 *
	 * @return void
	 */
	public function render_chart( $id, $type, $data, $options = [] ) {
		$id = 'progress-planner-chart-' . $id;

		$options['responsive'] = true;

		// TODO: This should be properly enqueued.
		// phpcs:ignore
		echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
		?>

		<canvas id="<?php echo \sanitize_key( $id ); ?>" style="max-height:500px;"></canvas>
		<script>
			var chart = new Chart( document.getElementById( '<?php echo \sanitize_key( $id ); ?>' ), {
				type: '<?php echo \esc_js( $type ); ?>',
				data: <?php echo \wp_json_encode( $data ); ?>,
				options: <?php echo \wp_json_encode( $options ); ?>,
			} );
		</script>
		<?php
	}
}
