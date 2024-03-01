<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/*
 * Get the content score.
 */
$prpl_content_count = count(
	array_merge(
		\progress_planner()->get_query()->query_activities(
			[
				'start_date' => new \DateTime( '-7 days' ),
				'end_date'   => new \DateTime( 'tomorrow' ),
				'category'   => 'content',
			]
		),
		\progress_planner()->get_query()->query_activities(
			[
				'start_date' => new \DateTime( '-7 days' ),
				'end_date'   => new \DateTime( 'tomorrow' ),
				'category'   => 'comments',
			]
		)
	)
);
// Target 5 content activities per week.
$prpl_content_score = min( $prpl_content_count, 5 ) / 5;

/*
 * Get the maintenance score.
 */
$prpl_maintenance_count = count(
	\progress_planner()->get_query()->query_activities(
		[
			'start_date' => new \DateTime( '-7 days' ),
			'end_date'   => new \DateTime( 'tomorrow' ),
			'category'   => 'maintenance',
		]
	)
);
$prpl_pending_updates = wp_get_update_data()['counts']['total'];

// Target is the number of pending updates + the ones that have already been done.
$prpl_maintenance_score = max( 1, $prpl_maintenance_count ) / max( 1, $prpl_maintenance_count + $prpl_pending_updates );

/**
 * Calculate the score.
 */
$prpl_score = 0.7 * $prpl_content_score + 0.3 * $prpl_maintenance_score;

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
