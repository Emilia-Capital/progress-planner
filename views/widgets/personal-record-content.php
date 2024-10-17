<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

$record = $this->personal_record_callback();
?>
<div class="two-col narrow">
	<?php $this->render_big_counter( (int) $record['max_streak'], __( 'Personal record', 'progress-planner' ) ); ?>
	<div class="prpl-widget-content">
		<?php if ( (int) $record['max_streak'] === 0 ) : ?>
			<?php \esc_html_e( 'This is the start of your first streak! Add content to your site every week and set a personal record!', 'progress-planner' ); ?>
		<?php elseif ( (int) $record['max_streak'] <= (int) $record['current_streak'] ) : ?>
			<?php
			printf(
				\esc_html(
					/* translators: %s: number of weeks. */
					\_n(
						'Congratulations! You\'re on a streak! You\'ve consistently maintained your website for the past %s week! 🎉',
						'Congratulations! You\'re on a streak! You\'ve consistently maintained your website for the past %s weeks! 🎉',
						(int) $record['current_streak'],
						'progress-planner'
					)
				),
				\esc_html( \number_format_i18n( $record['current_streak'] ) )
			);
			?>
		<?php elseif ( 1 <= $record['current_streak'] ) : ?>
			<?php
			printf(
				\esc_html(
					/* translators: %1$s: number of weeks for the current streak. %2$s: number of weeks for the maximum streak. %3$s: The number of weeks to go in order to break the record. */
					\_n(
						'Keep it up! You\'ve consistently maintained your website for the past %1$s week. Your longest streak was %2$s weeks, %3$s more to go to break your record!',
						'Keep it up! You\'ve consistently maintained your website for the past %1$s weeks. Your longest streak was %2$s weeks, %3$s more to go to break your record!',
						(int) $record['current_streak'],
						'progress-planner'
					)
				),
				\esc_html( \number_format_i18n( $record['current_streak'] ) ),
				\esc_html( \number_format_i18n( $record['max_streak'] ) ),
				\esc_html( \number_format_i18n( $record['max_streak'] - $record['current_streak'] ) )
			);
			?>
		<?php else : ?>
			<?php
			printf(
				\esc_html(
					/* translators: %1$s: number of weeks for the maximum streak. */
					\_n(
						'Get back to your streak! Your longest streak was %s week. Keep working on those website maintenance tasks every week and break your record!',
						'Get back to your streak! Your longest streak was %s weeks. Keep working on those website maintenance tasks every week and break your record!',
						(int) $record['max_streak'],
						'progress-planner'
					)
				),
				\esc_html( \number_format_i18n( $record['max_streak'] ) )
			);
			?>
		<?php endif; ?>
	</div>
</div>
