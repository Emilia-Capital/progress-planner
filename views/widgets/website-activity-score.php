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
<div class="two-col">
	<?php $this->print_score_gauge(); ?>
	<div>
		<?php \esc_html_e( 'Your activity this week:', 'progress-planner' ); ?>
		<?php $this->print_weekly_activities_checklist(); ?>
	</div>
</div>
<?php
