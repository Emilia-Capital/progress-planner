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
	<?php
	$prpl_admin_page_columns = [
		[
			[
				'website-activity-score',
				'published-content-density',
				'published-content',
				// 'published-pages',
				// 'published-posts',
				'published-words',
			],
		],
		[
			[
				'activity-scores',
				'plugins',
				'badge-content',
				'badge-streak',
			],
			[
				'latest-badge',
				'badges-progress',
				'__filter-numbers',
			],
		],
	];
	?>


	<?php if ( $prpl_scan_pending ) : ?>
		<?php include 'admin-page-form-scan.php'; ?>
	<?php else : ?>
		<div class="prpl-widgets-container">
			<?php foreach ( $prpl_admin_page_columns as $prpl_column_main ) : ?>
				<div class="prpl-column-main">
					<?php foreach ( $prpl_column_main as $prpl_column ) : ?>
						<div class="prpl-column">
							<?php foreach ( $prpl_column as $prpl_widget ) : ?>
								<div class="prpl-widget-wrapper prpl-<?php echo esc_attr( $prpl_widget ); ?>">
									<?php include "widgets/{$prpl_widget}.php"; ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php include 'admin-page-debug.php'; ?>
		<hr>
	<?php endif; ?>
</div>
