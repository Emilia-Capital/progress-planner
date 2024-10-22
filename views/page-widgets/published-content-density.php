<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $progress_planner;

?>

<div class="prpl-counter-big-wrapper">
	<span class="counter-big-number">
		<?php echo \esc_html( \number_format_i18n( (int) $this->get_weekly_activities_density() ) ); ?>
	</span>
	<span class="counter-big-text">
		<?php echo \esc_html_e( 'content density', 'progress-planner' ); ?>
	</span>
</div>

<div class="prpl-widget-content">
	<p>
		<?php
		if ( 0 === $this->get_weekly_activities_density() ) {
			printf(
				/* translators: %s: All-time average number. */
				\esc_html__( 'Your overall content density average is %1$s.', 'progress-planner' ),
				\esc_html( \number_format_i18n( $this->get_all_activities_density() ) )
			);
		} else {
			printf(
				/* translators: %1$s: number of words/post published this week. %2$s: All-time average number. */
				\esc_html__( 'Your content has an average density of %1$s words per post in the last 7 days. Your overall content density average is %2$s.', 'progress-planner' ),
				\esc_html( \number_format_i18n( $this->get_weekly_activities_density() ) ),
				\esc_html( \number_format_i18n( $this->get_all_activities_density() ) )
			);
		}
		?>
	</p>
</div>
<div class="prpl-graph-wrapper">
	<?php $progress_planner->chart->the_chart( $this->get_chart_args() ); ?>
</div>
