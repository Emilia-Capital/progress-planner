<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_activities = \progress_planner()->get_query()->query_activities(
	[
		// Use 31 days to take into account
		// the activities score decay from previous activities.
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

$prpl_score = floor( $prpl_score );

$prpl_checklist = [
	[
		'label'    => esc_html__( 'Publish content', 'progress-planner' ),
		'callback' => function () {
			$events = \progress_planner()->get_query()->query_activities(
				[
					'start_date' => new \DateTime( '-7 days' ),
					'end_date'   => new \DateTime(),
					'category'   => 'content',
					'type'       => 'publish',
				]
			);
			return count( $events ) > 0;
		},
	],
	[
		'label'    => esc_html__( 'Update content', 'progress-planner' ),
		'callback' => function () {
			$events = \progress_planner()->get_query()->query_activities(
				[
					'start_date' => new \DateTime( '-7 days' ),
					'end_date'   => new \DateTime(),
					'category'   => 'content',
					'type'       => 'update',
				]
			);
			return count( $events ) > 0;
		},
	],
	[
		'label'    => 0 === wp_get_update_data()['counts']['total']
			? esc_html__( 'Perform all updates', 'progress-planner' )
			: '<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">' . esc_html__( 'Perform all updates', 'progress-planner' ) . '</a>',
		'callback' => function () {
			return ! wp_get_update_data()['counts']['total'];
		},
	],
];

?>
<h2 class="prpl-widget-title">
	<?php esc_html_e( 'Website activity score', 'progress-planner' ); ?>
</h2>

<div class="two-col">
	<div style="text-align: center;">
		<div class="prpl-activities-gauge-container">
			<div
				class="prpl-activities-gauge"
				style="
					--value:<?php echo esc_attr( $prpl_score / 100 ); ?>;
					--background: var(--prpl-background-orange);
					--max: 180deg;
					--start: 270deg;
					--color:<?php echo esc_attr( $prpl_gauge_color ); ?>"
			>
				<span class="prpl-gauge-number">
					<?php echo esc_html( $prpl_score ); ?>
				</span>
			</div>
		</div>
		<?php esc_html_e( 'Based on your activity over the past 30 days', 'progress-planner' ); ?>
	</div>
	<div>
		<?php esc_html_e( 'Over the past week:', 'progress-planner' ); ?>
		<ul>
			<?php foreach ( $prpl_checklist as $prpl_item ) : ?>
				<li class="prpl-checklist-item">
					<?php echo ( $prpl_item['callback']() ) ? '✔️' : '❌'; ?>
					<?php echo $prpl_item['label']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
