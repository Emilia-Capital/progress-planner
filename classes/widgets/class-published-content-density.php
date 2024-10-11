<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Activities\Content_Helpers;
use Progress_Planner\Chart;
use Progress_Planner\Widgets\Widget;

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
		<div class="prpl-top-counter-bottom-content">
			<?php $this->render_big_counter( (int) $this->get_weekly_activities_density(), __( 'content density', 'progress-planner' ) ); ?>
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
			'count_callback' => [ $this, 'count_density' ],
			'compound'       => false,
			'filter_results' => [ $this, 'filter_activities' ],
		];
	}

	/**
	 * Callback to filter the activities.
	 *
	 * @param \Progress_Planner\Activities\Content[] $activities The activities array.
	 *
	 * @return \Progress_Planner\Activities\Content[]
	 */
	public function filter_activities( $activities ) {
		return array_filter(
			$activities,
			function ( $activity ) {
				return \is_object( $activity->get_post() )
					&& \in_array( $activity->get_post()->post_type, Content_Helpers::get_post_types_names(), true );
			}
		);
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
			if ( ! $activity->get_post() ) {
				continue;
			}
			$words += Content_Helpers::get_word_count(
				$activity->get_post()->post_content,
				(int) $activity->data_id
			);
		}
		return $words;
	}

	/**
	 * Callback to count the density of the activities.
	 *
	 * Returns the average number of words per activity.
	 *
	 * @param \Progress_Planner\Activities\Content[] $activities The activities array.
	 *
	 * @return int
	 */
	public function count_density( $activities ) {
		$words = $this->count_words( $activities );
		$count = count( $activities );
		return (int) round( $words / max( 1, $count ) );
	}

	/**
	 * Get the density of all activities.
	 *
	 * @return int
	 */
	public function get_all_activities_density() {
		// Get the all-time average.
		static $density;
		if ( null === $density ) {
			$activities = $this->filter_activities(
				\progress_planner()->get_query()->query_activities(
					[
						'category' => 'content',
						'type'     => 'publish',
					]
				)
			);
			$density    = $this->count_density( $activities );
		}
		return $density;
	}

	/**
	 * Get the weekly activities density.
	 *
	 * @return int
	 */
	public function get_weekly_activities_density() {
		static $density;
		if ( null === $density ) {
			// Get the weekly average.
			$density = $this->count_density(
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
