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
	 * Build a chart for the stats.
	 *
	 * @param array $args The arguments for the chart.
	 *                    See `get_chart_data` for the available parameters.
	 *
	 * @return void
	 */
	public function the_chart( $args = [] ) {
		// Render the chart.
		$this->render_chart( $args['type'], $this->get_chart_data( $args ) );
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
				'data'            => [],
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
	 * Render the charts.
	 *
	 * @param string $type    The type of chart.
	 * @param array  $data    The data for the chart.
	 *
	 * @return void
	 */
	public function render_chart( $type, $data ) {
		switch ( $type ) {
			case 'bar':
				$this->render_bar_chart_native( $data );
				break;

			default:
				$this->render_line_chart_svg( $data );
				break;
		}
	}

	/**
	 * Render the contents of a bar chart.
	 *
	 * @param array $data The data for the chart.
	 *
	 * @return void
	 */
	public function render_bar_chart_native( $data ) {
		?>
		<div style="display: flex; max-width: 600px; height: 200px; width: 100%; align-items: flex-end; gap: 5px; margin: 1rem 0;">
			<?php foreach ( $data['datasets'][0]['data'] as $i => $score ) : ?>
				<div style="flex: auto; display: flex; flex-direction: column; justify-content: flex-end; height: 100%;">
					<div style="display:block;width:100%;height: <?php echo esc_attr( $score ); ?>%; background: <?php echo esc_attr( $data['datasets'][0]['backgroundColor'][ $i ] ); ?>" title="<?php echo esc_attr( $score ); ?>%"></div>
					<span style="text-align:center;display:block;width:100%;font-size: 0.75em;"><?php echo esc_html( $data['labels'][ $i ] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render the SVG contents of a line chart.
	 *
	 * @param array $data The data for the chart.
	 * @param int   $max  The maximum value for the chart.
	 *
	 * @return void
	 */
	public function render_line_chart_svg( $data, $max = 100 ) {
		$aspect_ratio = 2;
		$height       = 300;
		$axis_offset  = 16;
		$width        = $height * $aspect_ratio;
		$stroke_width = 4;

		echo '<svg viewBox="0 0 ' . (int) ( $height * $aspect_ratio + $axis_offset * 2 ) . ' ' . (int) ( $height + $axis_offset * 2 ) . '">';

		// Determine the maximum value for the chart.
		$max = max(
			array_map(
				function ( $dataset ) {
					return max( $dataset['data'] );
				},
				$data['datasets']
			)
		);
		$max = ( 100 > $max && 70 < $max ) ? 100 : $max;

		// Calculate the Y coordinate for a given value.
		$calc_y_coordinate = function ( $value ) use ( $max, $axis_offset, $height, $stroke_width ) {
			// Calculate scaled coordinates because of the offset and the aspect ratio.
			$multiplier   = ( $height - $axis_offset * 2 ) / $height;
			$y_coordinate = ( $max - $value * $multiplier ) * ( $height / $max ) + $axis_offset;
			// Offset the Y coordinate to account for the axis offset.
			$y_coordinate -= $axis_offset * 2;
			// Account for the stroke width.
			return $y_coordinate - $stroke_width / 2;
		};

		// Calculate the Y axis labels.
		// Take the maximum value and divide it by 4 to get the step.
		$y_labels_step = $max / 4;

		// Calculate the Y axis labels.
		$y_labels = [];
		if ( 100 === $max || 15 > $max ) {
			for ( $i = 0; $i <= 4; $i++ ) {
				$y_labels[] = (int) ( $y_labels_step * $i );
			}
		} else {
			// Round the values to the nearest 10.
			for ( $i = 0; $i <= 4; $i++ ) {
				$y_labels[] = (int) min( $max, round( $y_labels_step * $i, -1 ) );
			}
		}

		// Calculate the distance between the points in the X axis.
		// Calculate the distance between the points in the X axis.
		$x_distance_between_points = round(
			( $width - ( 2 * $axis_offset ) ) / ( count( $data['datasets'][0]['data'] ) - 1 ),
			0,
			PHP_ROUND_HALF_DOWN
		);
		?>

		<!-- X-axis line. -->
		<g><line
			x1="<?php echo (int) $axis_offset * 2; ?>"
			x2="<?php echo (int) $aspect_ratio * $height; ?>"
			y1="<?php echo (int) $height - $axis_offset; ?>"
			y2="<?php echo (int) $height - $axis_offset; ?>"
			stroke="var(--prpl-color-gray-2)"
			stroke-width="1"
			/>
		</g>

		<!-- Y-axis line. -->
		<g><line
			x1="<?php echo (int) $axis_offset * 2; ?>"
			x2="<?php echo (int) $axis_offset * 2; ?>"
			y1="<?php echo (int) $axis_offset; ?>"
			y2="<?php echo (int) $height - $axis_offset; ?>"
			stroke="var(--prpl-color-gray-2)"
			stroke-width="1"
			/>
		</g>

		<!-- X-axis labels and rulers. -->
		<?php
		$label_x_coordinate = 0;
		$labels_x_count     = count( $data['labels'] );
		$labels_x_divider   = intval( round( $labels_x_count / 6, 0, PHP_ROUND_HALF_UP ) );
		$i                  = 0;
		?>
		<?php foreach ( $data['labels'] as $label ) : ?>
			<?php $label_x_coordinate = $x_distance_between_points * $i + $axis_offset; ?>
			<?php ++$i; ?>
			<?php
			// Only allow up to 6 labels to prevent overlapping.
			// If there are more than 6 labels, find the alternate labels.
			if ( 6 < $labels_x_count && 1 !== $i && ( $i - 1 ) % $labels_x_divider !== 0 ) {
				continue;
			}
			?>
			<g><text
				class="x-axis-label"
				x="<?php echo (int) $label_x_coordinate; ?>"
				y="<?php echo (int) $height + $axis_offset; ?>"
			>
				<?php echo \esc_html( (string) $label ); ?>
			</text></g>

			<?php if ( 1 !== $i ) : ?>
				<g><line
					x1="<?php echo (int) $label_x_coordinate + $axis_offset; ?>"
					x2="<?php echo (int) $label_x_coordinate + $axis_offset; ?>"
					y1="<?php echo (int) $axis_offset; ?>"
					y2="<?php echo (int) $height - $axis_offset; ?>"
					stroke="var(--prpl-color-gray-1)"
					stroke-width="1"
				/></g>
			<?php endif; ?>
		<?php endforeach; ?>

		<!-- Y-axis labels and rulers. -->
		<?php $i = 0; ?>
		<?php foreach ( $y_labels as $y_label ) : ?>
			<?php $y_label_coordinate = $calc_y_coordinate( $y_label ); ?>
			<g><text
				class="y-axis-label"
				x="0"
				y="<?php echo (int) $y_label_coordinate + $axis_offset / 2; ?>"
			>
				<?php echo \esc_html( (string) $y_label ); ?>
			</text></g>
			<?php ++$i; ?>
			<?php if ( 1 !== $i ) : ?>
				<g><line
					x1="<?php echo (int) $axis_offset * 2; ?>"
					x2="<?php echo (int) $aspect_ratio * $height; ?>"
					y1="<?php echo (int) $y_label_coordinate; ?>"
					y2="<?php echo (int) $y_label_coordinate; ?>"
					stroke="var(--prpl-color-gray-2)"
					stroke-width="1"
				/></g>
			<?php endif; ?>
		<?php endforeach; ?>

		<!-- Line chart. -->
		<?php foreach ( $data['datasets'] as $dataset ) : ?>
			<?php
			$points       = [];
			$x_coordinate = $axis_offset * 2;
			foreach ( $dataset['data'] as $point ) {
				$points[]      = [ $x_coordinate, round( $calc_y_coordinate( $point ) ) ];
				$x_coordinate += $x_distance_between_points;
			}
			?>
			<g><polyline
				fill="none"
				stroke="<?php echo \esc_attr( $dataset['borderColor'][0] ); ?>"
				stroke-width="<?php echo (int) $stroke_width; ?>"
				points="
					<?php foreach ( $points as $i => $point ) : ?>
						<?php echo \esc_attr( implode( ',', $point ) ); ?>
					<?php endforeach; ?>
				"
			/></g>
		<?php endforeach; ?>
		<?php
		echo '</svg>';
	}
}
