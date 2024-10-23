<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

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
				global $progress_planner;
				$post = $activity->get_post();
				return is_object( $post )
					&& \in_array( $post->post_type, $progress_planner->get_helpers()->content->get_post_types_names(), true );
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
		global $progress_planner;
		$words = 0;
		foreach ( $activities as $activity ) {
			if ( ! $activity->get_post() ) {
				continue;
			}
			$words += $progress_planner->get_helpers()->content->get_word_count(
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
		global $progress_planner;
		// Get the all-time average.
		static $density;
		if ( null === $density ) {
			$activities = $this->filter_activities(
				$progress_planner->get_query()->query_activities(
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
		global $progress_planner;
		static $density;
		if ( null === $density ) {
			// Get the weekly average.
			$density = $this->count_density(
				$progress_planner->get_query()->query_activities(
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
