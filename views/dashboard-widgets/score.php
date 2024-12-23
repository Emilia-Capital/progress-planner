<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Badges\Monthly;

?>
<div class="prpl-dashboard-widget">
	<div>
		<prpl-gauge background="#fff" color="var(--prpl-color-accent-orange)" contentFontSize="var(--prpl-font-size-4xl)">
			<progress max="<?php echo (int) Monthly::TARGET_POINTS; ?>" value="<?php echo (float) \progress_planner()->get_widgets__suggested_tasks()->get_score(); ?>">
				<prpl-badge
					complete="<?php echo Monthly::TARGET_POINTS === (int) \progress_planner()->get_widgets__suggested_tasks()->get_score() ? 'true' : 'false'; ?>"
					badge-id="<?php echo esc_attr( 'monthly-' . gmdate( 'Y' ) . '-m' . (int) gmdate( 'm' ) ); ?>"
				></prpl-badge>
			</progress>
		</prpl-gauge>
		<?php \esc_html_e( 'Monthly badge', 'progress-planner' ); ?>
	</div>

	<div>
		<prpl-gauge background="#fff" color="<?php echo esc_attr( \progress_planner()->get_widgets__activity_scores()->get_gauge_color( \progress_planner()->get_widgets__activity_scores()->get_score() ) ); ?>" contentFontSize="var(--prpl-font-size-5xl)">
			<progress max="100" value="<?php echo (float) \progress_planner()->get_widgets__activity_scores()->get_score(); ?>">
				<?php echo \esc_html( \progress_planner()->get_widgets__activity_scores()->get_score() ); ?>
			</progress>
		</prpl-gauge>
		<?php \esc_html_e( 'Website activity score', 'progress-planner' ); ?>
	</div>
</div>

<hr style="margin: 1em 0">

<h3><?php \esc_html_e( 'Ravi\'s Recommendations', 'progress-planner' ); ?></h3>
<ul style="display:none"></ul>
<ul class="prpl-suggested-tasks-list"></ul>

<?php if ( \current_user_can( 'manage_options' ) ) : ?>
	<div class="prpl-dashboard-widget-footer">
		<img src="<?php echo \esc_attr( PROGRESS_PLANNER_URL . '/assets/images/icon_progress_planner.svg' ); ?>" style="width:1.5em;" alt="" />
		<a href="<?php echo \esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) ); ?>">
			<?php \esc_html_e( 'Check out all your stats and badges', 'progress-planner' ); ?>
		</a>
	</div>
<?php endif; ?>
