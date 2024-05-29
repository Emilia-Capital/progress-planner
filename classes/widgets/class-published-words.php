<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Activities\Content_Helpers;
use Progress_Planner\Chart;

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
		<div class="prpl-top-counter-bottom-content">
			<?php $this->render_big_counter( (int) $this->get_weekly_words(), __( 'Words', 'progress-planner' ) ); ?>
			<div class="prpl-widget-content">
				<p>
					<?php if ( 0 === $this->get_weekly_words() ) : ?>
						<?php \esc_html_e( 'You didn\'t write last week. Let\'s get started!', 'progress-planner' ); ?>
					<?php else : ?>
						<?php
						// @todo If I've only written one word, this should say "You've written 1 word in the past 7 days."
						printf(
							/* translators: %1$s: number of posts published this week. %2$s: Total number of posts. */
							\esc_html__( 'Great job! You have written %1$s words in the past 7 days.', 'progress-planner' ),
							\esc_html( \number_format_i18n( $this->get_weekly_words() ) ),
						);
						?>
					<?php endif; ?>
				</p>
			</div>
		</div>
		<div class="prpl-graph-wrapper">
			<?php ( new Chart() )->the_chart( $this->get_chart_args() ); ?>
		</div>
		<?php
	}

	/**
	 * Get the chart args.
	 *
	 * @return array The chart args.
	 */
	public function get_chart_args() {
		return [
			'query_params'   => [
				'category' => 'content',
				'type'     => 'publish',
			],
			'dates_params'   => [
				'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( $this->get_range() ),
				'end'       => new \DateTime(),
				'frequency' => $this->get_frequency(),
				'format'    => 'M',
			],
			'chart_params'   => [
				'type' => 'line',
			],
			'count_callback' => [ $this, 'count_words' ],
			'compound'       => false,
		];
	}

	/**
	 * Callback to count the words in the activities.
	 *
	 * @param \Progress_Planner\Activities\Content[] $activities The activities array.
	 *
	 * @return int
	 */
	public function count_words( $activities ) {
		$words = 0;
		foreach ( $activities as $activity ) {
			if ( null === $activity->get_post() ) {
				continue;
			}
			$words += Content_Helpers::get_word_count(
				$activity->get_post()->post_content,
				$activity->data_id
			);
		}
		return $words;
	}

	/**
	 * Get the weekly words count.
	 *
	 * @return int The weekly words count.
	 */
	public function get_weekly_words() {
		static $weekly_words;
		if ( null === $weekly_words ) {
			$weekly_words = $this->count_words(
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
