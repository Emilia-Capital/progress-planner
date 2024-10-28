<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget = \progress_planner()->get_admin()->page->get_widget( 'activity-scores' );
$record      = $prpl_widget->personal_record_callback();

// @todo "past months" should be "past month" if the website is not older than a month yet.
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your website activity score', 'progress-planner' ); ?>

	<div class="tooltip-actions">
		<button
			class="prpl-info-icon"
			onclick="this.closest( '.tooltip-actions' ).querySelector( '.prpl-tooltip' ).toggleAttribute( 'data-tooltip-visible' )"
		>
			<span class="dashicons dashicons-info-outline"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'More info', 'progress-planner' ); ?></span>
		</button>

		<div class="prpl-tooltip">
			<?php \esc_html_e( 'Your website activity score is based on the amount of website maintenance work you have done over the past 30 days.', 'progress-planner' ); ?>
		</div>
	</div>
</h2>

<div style="--background: var(--prpl-background-orange)">
	<?php \progress_planner()->the_view( 'page-widgets/parts/activity-scores-gauge.php' ); ?>
</div>

<hr>

<p><?php \esc_html_e( 'Check out your website activity in the past months:', 'progress-planner' ); ?></p>
<div class="prpl-graph-wrapper">
	<?php
	\progress_planner()->get_chart()->the_chart(
		[
			'query_params'   => [],
			'dates_params'   => [
				'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( $prpl_widget->get_range() ),
				'end'       => new \DateTime(),
				'frequency' => $prpl_widget->get_frequency(),
				'format'    => 'M',
			],
			'chart_params'   => [
				'type'    => 'bar',
				'options' => [
					'responsive'          => true,
					'maintainAspectRatio' => false,
					'pointStyle'          => false,
					'plugins'             => [
						'legend' => [
							'display' => false,
						],
					],
					'scales'              => [
						'yAxis' => [
							'min' => 0,
							'max' => 100,
						],
					],
				],
			],
			'count_callback' => function ( $activities, $date ) {
				$score = 0;
				foreach ( $activities as $activity ) {
					$score += $activity->get_points( $date );
				}
				return $score * 100 / Base::SCORE_TARGET;
			},
			'compound'       => false,
			'normalized'     => true,
			'colors'         => [
				'background' => [ $prpl_widget, 'get_color' ],
				'border'     => [ $prpl_widget, 'get_color' ],
			],
			'max'            => 100,
		]
	);
	?>
</div>

<hr>

<div class="prpl-counter-big-wrapper prpl-personal-record-content">
	<span class="counter-big-number">
		<?php echo \esc_html( \number_format_i18n( (int) $record['max_streak'] ) ); ?>
	</span>
	<span class="counter-big-text"><?php \esc_html_e( 'personal record', 'progress-planner' ); ?></span>
</div>

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
