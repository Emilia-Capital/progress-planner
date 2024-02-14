<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

// Get the stats object.
$prpl_stats_posts = new ProgressPlanner\Stats\Stat_Posts();

// phpcs:ignore WordPress.Security.NonceVerification.Missing
$prpl_filters_interval = isset( $_POST['interval'] ) ? sanitize_key( $_POST['interval'] ) : 'weeks';
// phpcs:ignore WordPress.Security.NonceVerification.Missing
$prpl_filters_number = isset( $_POST['number'] ) ? (int) $_POST['number'] : 10;

// Check if we have a scan pending.
$prpl_scan_pending  = false;
$prpl_scan_progress = 0;
if ( empty( $prpl_stats_posts->get_value() ) ) {
	$prpl_scan_pending = true;
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>

	<?php if ( $prpl_scan_pending ) : ?>
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
