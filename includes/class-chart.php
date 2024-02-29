<?php
/**
 * Generate charts for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Date;

/**
 * Render a chart.
 */
class Chart {

	/**
	 * Build a chart for the stats.
	 *
	 * @param array $args The arguments for the chart.
	 *                    ['query_params'] The query parameters.
	 *                                     See \ProgressPlanner\Query::query_activities for the available parameters.
	 *
	 *                    ['dates_params'] The dates parameters for the query.
	 *                                   ['start']     The start date for the chart.
	 *                                   ['end']       The end date for the chart.
	 *                                   ['frequency'] The frequency for the chart nodes.
	 *                                   ['format']    The format for the label
	 *
	 *                     ['chart_params'] The chart parameters.
	 *
	 *                     [additive]       Whether to add the stats for next node to the previous one.
	 *
	 * @return void
	 */
	public function the_chart( $args = [] ) {
		$args                 = wp_parse_args(
			$args,
			[
				'query_params'   => [],
				'dates_params'   => [],
				'chart_params'   => [],
				'additive'       => true,
				'colors'         => [
					'background' => function () {
						return '#534786';
					},
					'border'     => function () {
						return '#534786';
					},
				],
				'count_callback' => function ( $activities ) {
					return count( $activities );
				},
			]
		);
		$args['chart_params'] = wp_parse_args(
			$args['chart_params'],
			[
				'type'    => 'line',
				'options' => [
					'responsive'          => true,
					'maintainAspectRatio' => false,
					'pointStyle'          => false,
					'plugins'             => [
						'legend' => [
							'display' => false,
						],
					],
				],
			]
		);

		$periods = Date::get_periods(
			$args['dates_params']['start'],
			$args['dates_params']['end'],
			$args['dates_params']['frequency']
		);

		$data     = [
			'labels'   => [],
			'datasets' => [],
		];
		$datasets = [
			[
				'label'           => '',
				'data'            => [],
				'tension'         => 0.2,
				'backgroundColor' => [],
				'borderColor'     => [],
			],
		];

		// Calculate zero stats to be used as the baseline.
		$activities_count = $args['additive']
			? $args['count_callback'](
				\progress_planner()->get_query()->query_activities(
					array_merge(
						$args['query_params'],
						[
							'start_date' => \progress_planner()->get_query()->get_oldest_activity()->get_date(),
							'end_date'   => $periods[0]['dates'][0]->modify( '-1 day' ),
						]
					)
				)
			) : 0;

		foreach ( $periods as $period ) {
			$activities = \progress_planner()->get_query()->query_activities(
				array_merge(
					$args['query_params'],
					[
						'start_date' => $period['dates'][0],
						'end_date'   => end( $period['dates'] ),
					]
				)
			);

			$data['labels'][] = $period['dates'][0]->format( $args['dates_params']['format'] );

			$activities_count                 = $args['additive']
				? $activities_count + $args['count_callback']( $activities )
				: $args['count_callback']( $activities );
			$datasets[0]['data'][]            = $activities_count;
			$datasets[0]['backgroundColor'][] = $args['colors']['background']( $activities_count );
			$datasets[0]['borderColor'][]     = $args['colors']['border']( $activities_count );
		}
		$data['datasets'] = $datasets;

		$this->render_chart(
			md5( wp_json_encode( $args ) ),
			$args['chart_params']['type'],
			$data,
			$args['chart_params']['options']
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

		// TODO: This should be properly enqueued.
		// phpcs:ignore
		echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
		?>

		<div class="prpl-chart-container" style="position: relative; height:100%; width:100%">
			<canvas id="<?php echo \sanitize_key( $id ); ?>"></canvas>
		</div>
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
