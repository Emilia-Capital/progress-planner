<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget = \progress_planner()->get_admin__page()->get_widget( 'activity-scores' );

\progress_planner()->the_view(
	'page-widgets/parts/gauge.php',
	[
		'prpl_gauge_details' => [
			'value'      => $prpl_widget->get_score() / 100,
			'max'        => 100,
			'number'     => $prpl_widget->get_score(),
			'background' => 'var(--prpl-background-orange)',
			'color'      => $prpl_widget->get_gauge_color( $prpl_widget->get_score() ),
		],
	]
);
