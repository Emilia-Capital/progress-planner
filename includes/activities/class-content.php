<?php
/**
 * Handler for posts activities.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

use ProgressPlanner\Activity;
use ProgressPlanner\Date;
use ProgressPlanner\Activities\Content_Helpers;

/**
 * Handler for posts activities.
 */
class Content extends Activity {

	/**
	 * The points awarded for each activity.
	 *
	 * @var array
	 */
	const ACTIVITIES_POINTS = [
		'publish' => 50,
		'update'  => 20,
		'delete'  => 10,
		'comment' => 5,
	];

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	protected $category = 'content';

	/**
	 * Get WP_Post from the activity.
	 *
	 * @return \WP_Post
	 */
	public function get_post() {
		return \get_post( $this->data_id );
	}

	/**
	 * Get the points for an activity.
	 *
	 * @param \DateTime $date The date for which we want to get the points of the activity.
	 *
	 * @return int
	 */
	public function get_points( $date ) {
		$points = self::ACTIVITIES_POINTS[ $this->get_type() ];
		$post   = $this->get_post();
		if ( ! $post ) {
			return 0;
		}
		$words = Content_Helpers::get_word_count( $post->post_content );
		if ( $words > 1000 ) {
			$points -= 10;
		} elseif ( $words > 350 ) {
			$points += 5;
		} elseif ( $words > 100 ) {
			$points += 2;
		} else {
			$points -= 2;
		}

		$days = Date::get_days_between_dates( $date, $this->get_date() );

		// If $days is > 0, then the activity is in the future.
		if ( $days > 0 ) {
			return 0;
		}

		$days = absint( $days );

		// Maximum range for awarded points is 30 days.
		if ( $days >= 30 ) {
			return 0;
		}

		$points = ( $days < 7 )
			? round( $points ) // If the activity is new (less than 7 days old), award full points.
			: round( $points * ( 1 - $days / 30 ) ); // Decay the points based on the age of the activity.

		error_log( 'Days: ' . $days . ' Points: ' . $points );

		return $points;
	}
}
