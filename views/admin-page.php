<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

// TODO: Move this to a method to allow prepopulating stats from the admin page.
$prpl_prepopulate = new ProgressPlanner\Stats\Stat_Posts_Prepopulate();

// Get the stats object.
$prpl_stats_posts = new ProgressPlanner\Stats\Stat_Posts();

// var_dump($prpl_stats_posts->get_value());

// Check if we have a scan pending.
$prpl_scan_pending  = false;
$prpl_scan_progress = 0;
if ( ! $prpl_stats_posts->get_value( $prpl_prepopulate::FINISHED_KEY ) ) {
	$prpl_scan_pending = true;
}
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>

	<?php if ( $prpl_scan_pending ) : ?>
		<?php
		/**
		 * The scan is pending.
		 * Show the form to start the scan.
		 */
		?>
		<h2><?php esc_html_e( 'Scan existing content', 'progress-planner' ); ?></h2>
		<p><?php esc_html_e( 'Scan your existing content to prepopulate the stats.', 'progress-planner' ); ?></p>
		<form id="progress-planner-scan" method="post">
			<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Scan', 'progress-planner' ); ?>">
		</form>
		<div id="progress-planner-scan-progress" style="display:none;">
			<p><?php esc_html_e( 'Scanning...', 'progress-planner' ); ?></p>
			<progress value="<?php echo esc_attr( $prpl_scan_progress ); ?>" max="100"></progress>
		</div>
	<?php else : ?>
		<?php
		/**
		 * The scan is not pending.
		 *
		 * Show a form to reset the stats (while we're still in development).
		 *
		 * Show the stats.
		 */
		?>
		<form id="progress-planner-stats-reset" method="post">
			<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Reset Stats', 'progress-planner' ); ?>">
		</form>

		<hr>

		<details>
			<summary><?php esc_html_e( 'Debug', 'progress-planner' ); ?></summary>
			<pre><?php print_r( $prpl_stats_posts->get_value() ); ?></pre>
		</details>

		<hr>

		<h2><?php esc_html_e( 'Post Types', 'progress-planner' ); ?></h2>
		<div style="max-height: 300px;display:flex;">
			<div>
				<h3><?php esc_html_e( 'Posts count progress', 'progress-planner' ); ?></h3>
				<?php $prpl_stats_posts->build_chart( [], 'count', 'weeks', 10, 0 ); ?>
			</div>
			<div>
				<h3><?php esc_html_e( 'Words count progress', 'progress-planner' ); ?></h3>
				<?php $prpl_stats_posts->build_chart( [], 'words', 'weeks', 10, 0 ); ?>
			</div>
		</div>
	<?php endif; ?>
</div>
