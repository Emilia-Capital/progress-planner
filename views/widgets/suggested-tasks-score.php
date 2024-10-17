<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

?>

<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Suggested tasks score', 'progress-planner' ); ?>
</h2>
<?php $this->print_score_gauge(); ?>
