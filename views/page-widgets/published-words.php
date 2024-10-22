<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Chart;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="prpl-counter-big-wrapper">
	<span class="counter-big-number">
		<?php echo \esc_html( \number_format_i18n( (int) $this->get_weekly_words() ) ); ?>
	</span>
	<span class="counter-big-text">
		<?php echo \esc_html_e( 'words', 'progress-planner' ); ?>
	</span>
</div>

<div class="prpl-widget-content">
	<p>
		<?php if ( 0 === $this->get_weekly_words() ) : ?>
			<?php \esc_html_e( 'You didn\'t write last week. Let\'s get started!', 'progress-planner' ); ?>
		<?php else : ?>
			<?php
			printf(
				\esc_html(
					/* translators: %1$s: number of posts published this week. %2$s: Total number of posts. */
					\_n(
						'Great job! You have written %1$s word in the past 7 days.',
						'Great job! You have written %1$s words in the past 7 days.',
						$this->get_weekly_words(),
						'progress-planner'
					)
				),
				\esc_html( \number_format_i18n( $this->get_weekly_words() ) ),
			);
			?>
		<?php endif; ?>
	</p>
</div>
<div class="prpl-graph-wrapper">
	<?php ( new Chart() )->the_chart( $this->get_chart_args() ); ?>
</div>
<?php