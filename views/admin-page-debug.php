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
	<pre style="max-height:600px;overflow-y:scroll;color:#fff;background:#000;padding:2em">
		<?php
		//phpcs:ignore WordPress.PHP.DevelopmentFunctions
		print_r( \ProgressPlanner\Activities\Query::get_instance()->query_activities( [] ) );
		?>
	</pre>
</details>
