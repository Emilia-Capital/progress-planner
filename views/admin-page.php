<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

?>
<div class="wrap prpl-wrap">
	<h1><?php esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>

	<?php if ( \ProgressPlanner\Admin\Page::get_params()['scan_pending'] ) : ?>
		<?php include 'admin-page-form-scan.php'; ?>
	<?php else : ?>
		<?php include 'widget-published-posts.php'; ?>
		<?php include 'admin-page-debug.php'; ?>
		<hr>
	<?php endif; ?>
</div>
<?php
