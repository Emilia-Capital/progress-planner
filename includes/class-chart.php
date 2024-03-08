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
		/*
		 * Set default values for the arguments.
		 */
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
					'scales'              => [
						'yAxis' => [
							'ticks' => [
								'precision' => 0,
							],
						],
					],
					'plugins'             => [
						'legend' => [
							'display' => false,
						],
					],
				],
			]
		);

		// Get the periods for the chart.
		$periods = Date::get_periods(
			$args['dates_params']['start'],
			$args['dates_params']['end'],
			$args['dates_params']['frequency']
		);

		// Prepare the data for the chart.
		$data     = [
			'labels'   => [],
			'datasets' => [],
		];
		$datasets = [
			[
				'label'           => '',
				'xAxisID'         => 'xAxis',
				'yAxisID'         => 'yAxis',
				'data'            => [],
				'tension'         => 0.2,
				'backgroundColor' => [],
				'borderColor'     => [],
			],
		];

		/*
		 * Calculate zero stats to be used as the baseline.
		 *
		 * If this is an "additive" chart,
		 * we need to calculate the score for all activities before the first period.
		 */
		$score = 0;
		if ( $args['additive'] ) {
			$oldest_activity = \progress_planner()->get_query()->get_oldest_activity();
			if ( null !== $oldest_activity ) {
				// Get the activities before the first period.
				// We need to subtract one day from the start date to get the activities before the first period.
				$end_date = clone $args['dates_params']['start'];
				$end_date->modify( '-1 day' );
				// Get all activities before the first period.
				$activities = \progress_planner()->get_query()->query_activities(
					array_merge(
						$args['query_params'],
						[
							'start_date' => $oldest_activity->get_date(),
							'end_date'   => $end_date,
						]
					)
				);
				// Filter the results if a callback is provided.
				if ( $args['filter_results'] ) {
					$activities = $args['filter_results']( $activities );
				}
				// Calculate the score for the activities.
				$score = $args['count_callback']( $activities );
			}
		}

		/*
		 * "Normalized" charts decay the score of previous months activities,
		 * and add them to the current month score.
		 * This means that for "normalized" charts, we need to get activities
		 * for the month prior to the first period.
		 */
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

		// Loop through the periods and calculate the score for each period.
		foreach ( $periods as $period ) {
			// Get the activities for the period.
			$activities = \progress_planner()->get_query()->query_activities(
				array_merge(
					$args['query_params'],
					[
						'start_date' => $period['start'],
						'end_date'   => $period['end'],
					]
				)
			);
			// Filter the results if a callback is provided.
			if ( $args['filter_results'] ) {
				$activities = $args['filter_results']( $activities );
			}

			// Add the label for the period.
			$data['labels'][] = $period['start']->format( $args['dates_params']['format'] );

			// Calculate the score for the period.
			$period_score = $args['count_callback']( $activities, $period['start'] );

			// If this is a "normalized" chart, we need to calculate the score for the previous month activities.
			if ( $args['normalized'] ) {
				// Add the previous month activities to the current month score.
				$period_score += $args['count_callback']( $previous_month_activities, $period['start'] );
				// Update the previous month activities for the next iteration of the loop.
				$previous_month_activities = $activities;
			}

			// "Additive" charts add the score for the period to the previous score.
			$score = $args['additive'] ? $score + $period_score : $period_score;

			// Apply a "max" limit to the score if max is defined in the arguments.
			$datasets[0]['data'][] = null === $args['max']
				? $score
				: min( $score, $args['max'] );

			// Calculate the colors for the score.
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
