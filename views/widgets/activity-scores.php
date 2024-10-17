<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Chart;

// @todo "past months" should be "past month" if the website is not older than a month yet.
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Longterm activity scores', 'progress-planner' ); ?>
</h2>
<p><?php \esc_html_e( 'Check out your website activity in the past months:', 'progress-planner' ); ?></p>
<div class="prpl-graph-wrapper">
	<?php ( new Chart() )->the_chart( $this->get_chart_args() ); ?>
</div>
<?php

