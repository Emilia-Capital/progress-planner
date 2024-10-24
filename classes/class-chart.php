<?php
/**
 * Generate charts for the admin page.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

/**
 * Render a chart.
 */
class Chart {

	/**
	 * The default chart parameters.
	 *
	 * @var array<string, mixed>
	 */
	const DEFAULT_CHART_PARAMS = [
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
	];

	/**
	 * Build a chart for the stats.
	 *
	 * @param array $args The arguments for the chart.
	 *                    See `get_chart_data` for the available parameters.
	 *
	 * @return void
	 */
	public function the_chart( $args = [] ) {
		$chart_params = \wp_parse_args( $args['chart_params'], static::DEFAULT_CHART_PARAMS );

		// Render the chart.
		$this->render_chart_js(
			md5( \wp_json_encode( $args ) ) . \wp_rand( 0, 1000 ),
			$chart_params['type'],
			$this->get_chart_data( $args ),
			$chart_params['options']
		);
	}

	/**
	 * Get data for the chart.
	 *
	 * @param array $args The arguments for the chart.
	 *                    ['query_params']   The query parameters.
	 *                                       See \Progress_Planner\Query::query_activities for the available parameters.
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
	 *                     [compound]       Whether to add the stats for next node to the previous one.
	 *
	 * @return array
	 */
	public function get_chart_data( $args = [] ) {
		$activities = [];

		/*
		 * Set default values for the arguments.
		 */
		$args = \wp_parse_args(
			$args,
			[
				'query_params'   => [],
				'filter_results' => null,
				'dates_params'   => [],
				'chart_params'   => [],
				'compound'       => false,
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

		// Get the periods for the chart.
		$periods = \progress_planner()->get_date()->get_periods(
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
		 * If this is an "compound" chart,
		 * we need to calculate the score for all activities before the first period.
		 */
		$score = 0;
		if ( $args['compound'] ) {
			$oldest_activity = \progress_planner()->get_query()->get_oldest_activity();
			if ( null !== $oldest_activity ) {
				// Get the activities before the first period.
				// We need to subtract one day from the start date to get the activities before the first period.
				// Get all activities before the first period.
				$activities = \progress_planner()->get_query()->query_activities(
					array_merge(
						$args['query_params'],
						[
							'start_date' => $oldest_activity->date,
							'end_date'   => ( clone $periods[0]['start'] )->modify( '-1 day' ),
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
			$previous_month_start      = ( clone $periods[0]['start'] )->modify( '-1 month' );
			$previous_month_end        = ( clone $periods[0]['start'] )->modify( '-1 day' );
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
			$period_data = $this->get_period_data( $period, $data, $datasets, $args, $score, $previous_month_activities );

			$data                      = $period_data['data'];
			$score                     = $period_data['score'];
			$datasets                  = $period_data['datasets'];
			$previous_month_activities = $period_data['previous_month_activities'];
		}
		$data['datasets'] = $datasets;

		return $data;
	}

	/**
	 * Get the data for a period.
	 *
	 * @param array $period                    The period.
	 * @param array $data                      The data for the chart.
	 * @param array $datasets                  The datasets for the chart.
	 * @param array $args                      The arguments for the chart.
	 * @param int   $score                     The score for the period.
	 * @param array $previous_month_activities The activities for the previous month.
	 *
	 * @return array
	 */
	public function get_period_data( $period, $data, $datasets, $args, $score, $previous_month_activities ) {
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
		$score = $args['compound'] ? $score + $period_score : $period_score;

		// Apply a "max" limit to the score if max is defined in the arguments.
		$datasets[0]['data'][] = null === $args['max']
			? $score
			: min( $score, $args['max'] );

		// Calculate the colors for the score.
		$datasets[0]['backgroundColor'][] = $args['colors']['background']( $score, $period['start'] );
		$datasets[0]['borderColor'][]     = $args['colors']['border']( $score, $period['start'] );

		return [
			'data'                      => $data,
			'datasets'                  => $datasets,
			'score'                     => $score,
			'previous_month_activities' => $previous_month_activities,
		];
	}

	/**
	 * Render the chart using Chart.js.
	 *
	 * @param string $id      The ID of the chart.
	 * @param string $type    The type of chart.
	 * @param array  $data    The data for the chart.
	 * @param array  $options The options for the chart.
	 *
	 * @return void
	 */
	public function render_chart_js( $id, $type, $data, $options = [] ) {
		$id = 'progress-planner-chart-' . $id;
		?>

		<div class="prpl-chart-container">
			<canvas id="<?php echo \sanitize_key( $id ); ?>"></canvas>
		</div>
		<script>
			var chartOptions = <?php echo \wp_json_encode( $options ); ?>,
				previousXAxisLabel = '';

			chartOptions.scales = chartOptions.scales || {};
			chartOptions.scales.xAxis = chartOptions.scales.xAxis || {};
			chartOptions.scales.xAxis.ticks = {
				callback: function( val, index ) {
					var label = this.getLabelForValue( val );
					if ( label !== previousXAxisLabel ) {
						previousXAxisLabel = label;
						return label;
					}
				},
			};

			var chart = new Chart( document.getElementById( '<?php echo \sanitize_key( $id ); ?>' ), {
				type: '<?php echo \esc_js( $type ); ?>',
				data: <?php echo \wp_json_encode( $data ); ?>,
				options: chartOptions,
			} );
		</script>
		<?php
	}
}
