<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your website activity score', 'progress-planner' ); ?>
</h2>
<?php $this->print_score_gauge(); ?>
