<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Callback to calculate the color of the chart.
 *
 * @param int $number The number to calculate the color for.
 *
 * @return string The color.
 */
$prpl_color_callback = function ( $number ) {
	if ( $number > 90 ) {
		return '#14b8a6';
	}
	if ( $number > 30 ) {
		return '#faa310';
	}
	return '#f43f5e';
};

?>
<h2 class="prpl-widget-title">
	<?php esc_html_e( 'Activity scores', 'progress-planner' ); ?>
</h2>
<div class="prpl-graph-wrapper">
	<?php
	( new Chart() )->the_chart(
		[
			'query_params'   => [],
			'dates_params'   => [
				'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( '-24 months' ),
				'end'       => new \DateTime(),
				'frequency' => 'monthly',
				'format'    => 'M',
			],
			'chart_params'   => [
				'type' => 'bar',
			],
			'count_callback' => function ( $activities, $date ) {
				$score = 0;
				foreach ( $activities as $activity ) {
					$score += $activity->get_points( $date );
				}
				return round( min( 100, $score ) );
			},
			'additive'       => false,
			'rolling'        => true,
			'colors'         => [
				'background' => $prpl_color_callback,
				'border'     => $prpl_color_callback,
			],
		]
	);
	?>
</div>
