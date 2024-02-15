<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>

	<?php if ( \ProgressPlanner\Admin\Page::get_params()['scan_pending'] ) : ?>
		<?php include 'admin-page-form-scan.php'; ?>
	<?php else : ?>
		<?php include 'admin-page-form-filters.php'; ?>
		<hr>
		<div style="max-width:1000px;">
			<?php include 'admin-page-posts-count-progress.php'; ?>
		</div>
		<div style="max-width:1000px;">
			<?php include 'admin-page-words-count-progress.php'; ?>
		</div>
		<hr>
		<?php include 'admin-page-debug.php'; ?>
		<hr>
	<?php endif; ?>
</div>
<?php
