<?php
/**
 * A form to trigger the posts scanning.
 *
 * @package ProgressPlanner
 */

?>
<h2><?php esc_html_e( 'Scan existing content', 'progress-planner' ); ?></h2>
<p><?php esc_html_e( 'Scan your existing content to prepopulate the stats.', 'progress-planner' ); ?></p>
<form id="progress-planner-scan" method="post">
	<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Scan', 'progress-planner' ); ?>">
</form>
<div id="progress-planner-scan-progress" style="display:none;">
	<p><?php esc_html_e( 'Scanning...', 'progress-planner' ); ?></p>
	<progress value="0" max="100"></progress>
</div>
