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

// Values for the graph filters.
$prpl_filters_intervals = [
	'days'   => __( 'Days', 'progress-planner' ),
	'weeks'  => __( 'Weeks', 'progress-planner' ),
	'months' => __( 'Months', 'progress-planner' ),
];
$prpl_filters_interval  = isset( $_POST['interval'] ) ? sanitize_key( $_POST['interval'] ) : 'weeks';
$prpl_filters_number    = isset( $_POST['number'] ) ? (int) $_POST['number'] : 10;

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
		 * Show the stats, and at the end a form to reset the stats
		 * (while we're still in development).
		 */
		?>
		<div class="progress-planner-form-filters">
			<p><?php esc_html_e( 'Filter results', 'progress-planner' ); ?></p>
			<form method="POST" style="display:flex;">
				<select name="interval">
					<?php foreach ( $prpl_filters_intervals as $prpl_interval_name => $prpl_interval_label ) : ?>
						<option
							name="<?php echo esc_attr( $prpl_interval_name ); ?>"
							<?php echo $prpl_interval_name === $prpl_filters_interval ? ' selected' : ''; ?>
						><?php echo esc_html( $prpl_interval_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<input name="number" type="number" value="<?php echo esc_attr( $prpl_filters_number ); ?>">
				<input type="submit" class="button button-secondary" value="<?php esc_attr_e( 'Update' ); ?>">
			</form>
		</div>

		<hr>

		<h2><?php esc_html_e( 'Post Types', 'progress-planner' ); ?></h2>
		<div style="max-width:1000px;">
			<h3><?php esc_html_e( 'Posts count progress', 'progress-planner' ); ?></h3>
			<?php $prpl_stats_posts->build_chart( [], 'count', $prpl_filters_interval, $prpl_filters_number, 0 ); ?>
		</div>
		<div style="max-width:1000px;">
			<h3><?php esc_html_e( 'Words count progress', 'progress-planner' ); ?></h3>
			<?php $prpl_stats_posts->build_chart( [], 'words', $prpl_filters_interval, $prpl_filters_number, 0 ); ?>
		</div>

		<hr>
		<div style="height:200px;"></div>
		<form id="progress-planner-stats-reset" method="post">
			<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Reset Stats', 'progress-planner' ); ?>">
		</form>
		<hr>
		<details>
			<summary><?php esc_html_e( 'Raw data', 'progress-planner' ); ?></summary>
			<pre><?php print_r( $prpl_stats_posts->get_value() ); ?></pre>
		</details>
		<hr>
	<?php endif; ?>
</div>
<?php
