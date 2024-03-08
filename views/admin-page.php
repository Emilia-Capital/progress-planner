<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_scan_pending = null === \progress_planner()->get_query()->get_oldest_activity();
?>
<div class="wrap prpl-wrap">
	<h1 class="screen-reader-text"><?php \esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>
	<?php require 'admin-page-header.php'; ?>

	<?php if ( $prpl_scan_pending ) : ?>
		<?php include 'admin-page-form-scan.php'; ?>
	<?php else : ?>
		<div class="prpl-widgets-container">
			<?php
			foreach ( [
				'website-activity-score',
				'activity-scores',
				// 'latest-badge',
				// 'published-pages',
				// 'published-posts',
				'published-content-density',
				'published-content',
				'published-words',
				'badges-progress',
				'__filter-numbers',
			] as $prpl_widget ) {
				echo '<div class="prpl-widget-wrapper prpl-' . esc_attr( $prpl_widget ) . '">';
				include "widgets/{$prpl_widget}.php";
				echo '</div>';
			}
			?>
		</div>
		<?php include 'admin-page-debug.php'; ?>
		<hr>
	<?php endif; ?>
</div>
