<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_activities = \progress_planner()->get_query()->query_activities(
	[
		'start_date' => new \DateTime( '-31 days' ),
		'end_date'   => new \DateTime(),
	]
);

$prpl_score        = 0;
$prpl_current_date = new \DateTime();
foreach ( $prpl_activities as $prpl_activity ) {
	$prpl_score += $prpl_activity->get_points( $prpl_current_date ) / 2;
}
$prpl_score = min( 100, max( 0, $prpl_score / 2 ) );

// Get the number of pending updates.
$prpl_pending_updates = wp_get_update_data()['counts']['total'];

// Reduce points for pending updates.
$prpl_pending_updates_penalty = min( min( $prpl_score / 2, 25 ), $prpl_pending_updates * 5 );
$prpl_score                  -= $prpl_pending_updates_penalty;

// Calculate the color.
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
