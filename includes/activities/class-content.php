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

		$dev_config = \progress_planner()->get_dev_config();
		$points     = isset( $dev_config[ $this->get_type() ] )
			? $dev_config[ $this->get_type() ]
			: $dev_config['content-publish'];
		$post       = $this->get_post();

		if ( ! $post ) {
			return 0;
		}
		$words = Content_Helpers::get_word_count( $post->post_content );
		if ( $words > 1000 ) {
			$points *= $dev_config['content-1000-plus-words-multiplier'];
		} elseif ( $words > 350 ) {
			$points *= $dev_config['content-350-plus-words-multiplier'];
		} elseif ( $words > 100 ) {
			$points *= $dev_config['content-100-plus-words-multiplier'];
		} else {
			$points *= $dev_config['content-100-minus-words-multiplier'];
		}

		$days = absint( Date::get_days_between_dates( $date, $this->get_date() ) );

		// Maximum range for awarded points is 30 days.
		if ( $days >= 30 ) {
			return 0;
		}

		$points = ( $days < 7 )
			? round( $points ) // If the activity is new (less than 7 days old), award full points.
			: round( $points * max( 0, ( 1 - $days / 30 ) ) ); // Decay the points based on the age of the activity.

		return $points;
	}
}
