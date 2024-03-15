<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_personal_record_content = \progress_planner()->get_badges()->get_badge_progress( 'personal_record_content' );

?>
<div class="prpl-top-counter-bottom-content">
	<div class="counter-big-wrapper">
		<span class="counter-big-number">
			<?php echo esc_html( number_format_i18n( $prpl_personal_record_content['max_streak'] ) ); ?>
		</span>
		<span class="counter-big-text">
			<?php esc_html_e( 'personal record', 'progress-planner' ); ?>
		</span>
	</div>
	<div class="prpl-widget-content">
		<p>
			<?php if ( (int) $prpl_personal_record_content['max_streak'] <= (int) $prpl_personal_record_content['current_streak'] ) : ?>
				<?php
				printf(
					/* translators: %s: number of weeks. */
					esc_html__( 'Congratulations, you\'re on a streak! You have been adding new content to your site consistently every week for the past %s weeks! 🎉', 'progress-planner' ),
					esc_html( number_format_i18n( $prpl_personal_record_content['current_streak'] ) )
				);
				?>
			<?php elseif ( 1 < $prpl_personal_record_content['current_streak'] ) : ?>
				<?php
				printf(
					/* translators: %1$s: number of weeks for the current streak. %2$s: number of weeks for the maximum streak. %3$s: The number of weeks to go in order to break the record. */
					esc_html__( 'Keep it up! You have been adding content to your site consistently every week for the past %1$s weeks! Your longest streaks was %2$s weeks, you have %3$s weeks to go to break your record!', 'progress-planner' ),
					esc_html( number_format_i18n( $prpl_personal_record_content['current_streak'] ) ),
					esc_html( number_format_i18n( $prpl_personal_record_content['max_streak'] ) ),
					esc_html( number_format_i18n( $prpl_personal_record_content['max_streak'] - $prpl_personal_record_content['current_streak'] ) )
				);
				?>
			<?php else : ?>
				<?php
				printf(
					/* translators: %s: number of weeks for the maximum streak. */
					esc_html__( 'Your longest streaks was %s weeks. Keep adding content to your site every week and break your record!', 'progress-planner' ),
					esc_html( number_format_i18n( $prpl_personal_record_content['max_streak'] ) ),
				);
				?>
			<?php endif; ?>
		</p>
	</div>
</div>
