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
	 *                    ['query_params']   The query parameters.
	 *                                       See \ProgressPlanner\Query::query_activities for the available parameters.
	 *
	 *                    ['filter_results'] The callback to filter the results. Leave empty/null to skip filtering.
	 *                    ['dates_params']   The dates parameters for the query.
	 *                                    ['start']     The start date for the chart.
	 *                                    ['end']       The end date for the chart.
	 *                                    ['frequency'] The frequency for the chart nodes.
	 *                                    ['format']    The format for the label
	 *
	 *                     ['chart_params'] The chart parameters.
	 *
	 *                     [additive]       Whether to add the stats for next node to the previous one.
	 *
	 * @return void
	 */
	public function the_chart( $args = [] ) {
		$args = wp_parse_args(
			$args,
			[
				'query_params'   => [],
				'filter_results' => null,
				'dates_params'   => [],
				'chart_params'   => [],
				'additive'       => false,
				'normalized'     => false,
				'colors'         => [
					'background' => function () {
						return '#534786';
					},
					'border'     => function () {
						return '#534786';
					},
				],
				// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
				'count_callback' => function ( $activities, $date = null ) {
					return count( $activities );
				},
				'max'            => null,
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
		$score = 0;
		if ( $args['additive'] ) {
			$oldest_activity = \progress_planner()->get_query()->get_oldest_activity();
			if ( null !== $oldest_activity ) {
				$end_date   = clone $periods[0]['dates'][0];
				$activities = \progress_planner()->get_query()->query_activities(
					array_merge(
						$args['query_params'],
						[
							'start_date' => $oldest_activity->get_date(),
							'end_date'   => $end_date,
						]
					)
				);
				if ( $args['filter_results'] ) {
					$activities = $args['filter_results']( $activities );
				}
				$score = $args['count_callback']( $activities );
			}
		}

		$previous_month_activities = [];
		if ( $args['normalized'] ) {
			$previous_month_start = clone $periods[0]['start'];
			$previous_month_start->modify( '-1 month' );
			$previous_month_end        = clone $periods[0]['start'];
			$previous_month_activities = \progress_planner()->get_query()->query_activities(
				array_merge(
					$args['query_params'],
					[
						'start_date' => $previous_month_start,
						'end_date'   => $previous_month_end,
					]
				)
			);
			if ( $args['filter_results'] ) {
				$activities = $args['filter_results']( $activities );
			}
		}

		foreach ( $periods as $period ) {
			$activities = \progress_planner()->get_query()->query_activities(
				array_merge(
					$args['query_params'],
					[
						'start_date' => $period['start'],
						'end_date'   => $period['end']->modify( '-1 day' ),
					]
				)
			);
			if ( $args['filter_results'] ) {
				$activities = $args['filter_results']( $activities );
			}

			$data['labels'][] = $period['dates'][0]->format( $args['dates_params']['format'] );

			$period_score = $args['count_callback']( $activities, $period['start'] );
			if ( $args['normalized'] ) {
				// Add the previous month activities to the current month score.
				$period_score += $args['count_callback']( $previous_month_activities, $period['start'] );
				// Update the previous month activities for the next iteration of the loop.
				$previous_month_activities = $activities;
			}

			$score = $args['additive'] ? $score + $period_score : $period_score;

			$datasets[0]['data'][]            = null === $args['max']
				? $score
				: min( $score, $args['max'] );
			$datasets[0]['backgroundColor'][] = $args['colors']['background']( $score );
			$datasets[0]['borderColor'][]     = $args['colors']['border']( $score );
		}
		$data['datasets'] = $datasets;

		$this->render_chart(
			md5( wp_json_encode( $args ) ) . wp_rand( 0, 1000 ),
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
