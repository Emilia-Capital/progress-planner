<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_scan_pending = empty(
	\progress_planner()->get_query()->query_activities(
		[
			'category' => 'content',
			'type'     => 'publish',
		]
	)
);
?>
<div class="wrap prpl-wrap">
	<h1><?php \esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>

	<?php if ( $prpl_scan_pending ) : ?>
		<?php include 'admin-page-form-scan.php'; ?>
	<?php else : ?>
		<div class="prpl-widgets-container">
			<div class="main">
				<?php
				foreach ( [
					'website-activity-score',
					'published-content',
					'published-pages',
					'published-posts',
					'published-words',
					'published-content-density',
				] as $prpl_widget ) {
					echo '<div class="prpl-widget-wrapper prpl-' . esc_attr( $prpl_widget ) . '">';
					include "widgets/{$prpl_widget}.php";
					echo '</div>';
				}
				?>
			</div>
			<div class="secondary">
				<?php
				foreach ( [
					'activity-scores',
					'latest-badge',
				] as $prpl_widget ) {
					echo '<div class="prpl-widget-wrapper prpl-' . esc_attr( $prpl_widget ) . '">';
					include "widgets/{$prpl_widget}.php";
					echo '</div>';
				}
				?>
			</div>
		</div>
		<?php include 'admin-page-debug.php'; ?>
		<hr>
	<?php endif; ?>
</div>
