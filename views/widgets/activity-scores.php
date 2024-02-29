<?php
/**
 * Print the graph for words count progress.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

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
<div class="prpl-widget-wrapper">
	<h2 class="prpl-widget-title">
		<?php esc_html_e( 'Activity scores', 'progress-planner' ); ?>
	</h2>
	<div class="prpl-graph-wrapper">
		<?php
		( new Chart() )->the_chart(
			[
				'query_params' => [],
				'dates_params' => [
					'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( '-11 months' ),
					'end'       => new \DateTime(),
					'frequency' => 'monthly',
					'format'    => 'M',
				],
				'chart_params' => [
					'type' => 'bar',
				],
				'additive'     => false,
				'colors'       => [
					'background' => $prpl_color_callback,
					'border'     => $prpl_color_callback,
				],
			]
		);
		?>
	</div>
</div>
