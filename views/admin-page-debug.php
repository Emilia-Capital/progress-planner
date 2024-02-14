<?php
/**
 * Debug info and actions.
 *
 * @package ProgressPlanner
 */

?>
<div style="height:200px;"></div>
<form id="progress-planner-stats-reset" method="post">
	<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Reset Stats', 'progress-planner' ); ?>">
</form>
<hr>
<details>
	<summary><?php esc_html_e( 'Raw data', 'progress-planner' ); ?></summary>
	<pre><?php print_r( $prpl_stats_posts->get_value() ); ?></pre>
</details>
