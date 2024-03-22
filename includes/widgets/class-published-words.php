<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Activities\Content_Helpers;
use ProgressPlanner\Chart;

/**
 * Published Content Widget.
 */
final class Published_Words extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'published-words';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		?>
		<div class="two-col">
			<div class="prpl-top-counter-bottom-content">
				<div class="counter-big-wrapper">
					<span class="counter-big-number">
						<?php echo \esc_html( \number_format_i18n( self::get_weekly_words() ) ); ?>
					</span>
					<span class="counter-big-text">
						<?php \esc_html_e( 'words written', 'progress-planner' ); ?>
					</span>
				</div>
				<div class="prpl-widget-content">
					<p>
						<?php if ( 0 === self::get_weekly_words() ) : ?>
							<?php \esc_html_e( 'No words written last week', 'progress-planner' ); ?>
						<?php else : ?>
							<?php
							printf(
								/* translators: %1$s: number of posts published this week. %2$s: Total number of posts. */
								\esc_html__( 'Great! You have written %1$s words in the past 7 days.', 'progress-planner' ),
								\esc_html( \number_format_i18n( self::get_weekly_words() ) ),
							);
							?>
						<?php endif; ?>
					</p>
				</div>
			</div>
			<div class="prpl-graph-wrapper">
				<?php ( new Chart() )->the_chart( self::get_chart_args() ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the chart args.
	 *
	 * @return array The chart args.
	 */
	public static function get_chart_args() {
		return [
			'query_params'   => [
				'category' => 'content',
				'type'     => 'publish',
			],
			'dates_params'   => [
				'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( self::get_range() ),
				'end'       => new \DateTime(),
				'frequency' => self::get_frequency(),
				'format'    => 'M',
			],
			'chart_params'   => [
				'type' => 'line',
			],
			'count_callback' => [ self::class, 'count_words' ],
			'compound'       => false,
			'colors'         => [
				'background' => function () {
					return '#14b8a6';
				},
				'border'     => function () {
					return '#14b8a6';
				},
			],
		];
	}

	/**
	 * Callback to count the words in the activities.
	 *
	 * @param \ProgressPlanner\Activity[] $activities The activities array.
	 *
	 * @return int
	 */
	public static function count_words( $activities ) {
		$words = 0;
		foreach ( $activities as $activity ) {
			if ( ! $activity->get_post() ) {
				continue;
			}
			$words += Content_Helpers::get_word_count(
				$activity->get_post()->post_content,
				$activity->get_data_id()
			);
		}
		return $words;
	}

	/**
	 * Get the weekly words count.
	 *
	 * @return int The weekly words count.
	 */
	public static function get_weekly_words() {
		static $weekly_words;
		if ( null === $weekly_words ) {
			$weekly_words = self::count_words(
				\progress_planner()->get_query()->query_activities(
					[
						'category'   => 'content',
						'type'       => 'publish',
						'start_date' => new \DateTime( '-7 days' ),
					]
				)
			);
		}
		return $weekly_words;
	}
}
