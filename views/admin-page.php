<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Settings;

$prpl_existing_content_scanned = Settings::get( 'content_scanned', false );
?>
<div class="wrap prpl-wrap">
	<h1 class="screen-reader-text"><?php \esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>
	<?php require 'admin-page-header.php'; ?>
	<?php
	$prpl_admin_page_columns = [
		'primary'   => [
			'first' => [
				'website-activity-score',
				'published-content-density',
				'published-content',
				// 'published-pages',
				// 'published-posts',
				'published-words',
			],
		],
		'secondary' => [
			'first'  => [
				'activity-scores',
				'personal-record-content',
				'plugins',
				'badge-content',
				'badge-streak',
			],
			'second' => [
				'latest-badge',
				'badges-progress',
				'whats-new',
				'__filter-numbers',
			],
		],
	];
	?>

	<div class="prpl-widgets-container">
		<?php foreach ( $prpl_admin_page_columns as $prpl_column_main_key => $prpl_column_main ) : ?>
			<div class="prpl-column-main prpl-column-main-<?php echo esc_attr( $prpl_column_main_key ); ?>">
				<?php foreach ( $prpl_column_main as $prpl_column_key => $prpl_column ) : ?>
					<div class="prpl-column prpl-column-<?php echo esc_attr( $prpl_column_key ); ?>">
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
	<?php if ( ! Settings::get( 'content_scanned', false ) ) : ?>
		<?php include 'admin-page-form-scan.php'; ?>
	<?php endif; ?>
	<hr>
	<?php require 'admin-page-debug.php'; ?>
	<hr>
</div>
