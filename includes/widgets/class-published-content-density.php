<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Activities\Content_Helpers;
use ProgressPlanner\Chart;
use ProgressPlanner\Widgets\Widget;

/**
 * Published Content Density Widget.
 */
final class Published_Content_Density extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'published-content-density';

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
						<?php echo \esc_html( \number_format_i18n( self::get_weekly_activities_density() ) ); ?>
					</span>
					<span class="counter-big-text">
						<?php \esc_html_e( 'content density', 'progress-planner' ); ?>
					</span>
				</div>
				<div class="prpl-widget-content">
					<p>
						<?php
						printf(
							/* translators: %1$s: number of words/post published this week. %2$s: All-time average number. */
							\esc_html__( 'You have written content with an average density of %1$s words/post in the past 7 days. Your all-time average is %2$s', 'progress-planner' ),
							\esc_html( \number_format_i18n( self::get_weekly_activities_density() ) ),
							\esc_html( \number_format_i18n( self::get_all_activities_density() ) )
						);
						?>
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
			'count_callback' => [ self::class, 'count_density' ],
			'compound'       => false,
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
	 * Callback to count the density of the activities.
	 *
	 * Returns the average number of words per activity.
	 *
	 * @param \ProgressPlanner\Activity[] $activities The activities array.
	 *
	 * @return int
	 */
	public static function count_density( $activities ) {
		$words = self::count_words( $activities );
		$count = count( $activities );
		return round( $words / max( 1, $count ) );
	}

	/**
	 * Get the density of all activities.
	 *
	 * @return int
	 */
	public static function get_all_activities_density() {
		// Get the all-time average.
		static $density;
		if ( null === $density ) {
			$density = self::count_density(
				\progress_planner()->get_query()->query_activities(
					[
						'category' => 'content',
						'type'     => 'publish',
					]
				)
			);
		}
		return $density;
	}

	/**
	 * Get the weekly activities density.
	 *
	 * @return int
	 */
	public static function get_weekly_activities_density() {
		static $density;
		if ( null === $density ) {
			// Get the weekly average.
			$density = self::count_density(
				\progress_planner()->get_query()->query_activities(
					[
						'category'   => 'content',
						'type'       => 'publish',
						'start_date' => new \DateTime( '-7 days' ),
					]
				)
			);
		}
		return $density;
	}
}
