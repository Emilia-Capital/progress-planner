<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_existing_content_scanned = get_option( 'progress_planner_content_scanned', false );
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
				'personal-record-content',
			],
			[
				'latest-badge',
				'badges-progress',
				'__filter-numbers',
			],
		],
	];
	?>


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
	<?php if ( ! get_option( 'progress_planner_content_scanned', false ) ) : ?>
		<?php include 'admin-page-form-scan.php'; ?>
	<?php endif; ?>
	<hr>
	<?php include 'admin-page-debug.php'; ?>
	<hr>
</div>
