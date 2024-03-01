<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_activities_weights = [
	'content'     => [
		'weight' => 0.7,
		'target' => 5,
	],
	'maintenance' => [
		'weight' => 0.2,
		'target' => 1,
	],
	'comments'    => [
		'weight' => 0.1,
		'target' => 1,
	],
];

$prpl_get_score = function ( $activities, $weight, $target ) {
	return $weight * min( count( $activities ), $target );
};

$prpl_score = 0;
foreach ( $prpl_activities_weights as $prpl_activity_category => $prpl_activity_category_data ) {
	$prpl_score += $prpl_activity_category_data['weight'] * min(
		count(
			\progress_planner()->get_query()->query_activities(
				[
					'start_date' => new \DateTime( '-7 days' ),
					'end_date'   => new \DateTime( 'tomorrow' ),
					'category'   => $prpl_activity_category,
				]
			)
		),
		$prpl_activity_category_data['target']
	) / $prpl_activity_category_data['target'];
}

$prpl_score       = round( 100 * $prpl_score );
$prpl_gauge_color = 'var(--prpl-color-accent-red)';
if ( $prpl_score > 50 ) {
	$prpl_gauge_color = 'var(--prpl-color-accent-orange)';
}
if ( $prpl_score > 75 ) {
	$prpl_gauge_color = 'var(--prpl-color-accent-green)';
}

?>
<h2 class="prpl-widget-title">
	<?php esc_html_e( 'Website activity score', 'progress-planner' ); ?>
</h2>

<div class="two-col">
	<div class="prpl-gauge-container" style="--percent: <?php echo esc_attr( $prpl_score ); ?>;--accent: <?php echo esc_attr( $prpl_gauge_color ); ?>;">
		<div class="prpl-gauge">
			<span class="prpl-gauge-number">
				<?php echo esc_html( $prpl_score ); ?>
			</span>
		</div>
	</div>

	<div>
		<p>Bla bla bla</p>
		<p>Bla bla bli</p>
		<p>Bla bla blo</p>
	</div>
</div>
