<?php
/**
 * Handler for posts activities.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

use ProgressPlanner\Base;
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
		$date_ymd = $date->format( 'Ymd' );
		if ( isset( $this->points[ $date_ymd ] ) ) {
			return $this->points[ $date_ymd ];
		}

		$this->points[ $date_ymd ] = Base::$points_config['content']['publish'];
		if ( isset( Base::$points_config['content'][ $this->get_type() ] ) ) {
			$this->points[ $date_ymd ] = Base::$points_config['content'][ $this->get_type() ];
		}
		$post = $this->get_post();

		if ( ! $post ) {
			$this->points[ $date_ymd ] = 0;
			return $this->points[ $date_ymd ];
		}
		$words = Content_Helpers::get_word_count( $post->post_content, $post->ID );
		if ( $words > 1000 ) {
			$this->points[ $date_ymd ] *= Base::$points_config['content']['word-multipliers'][1000];
		} elseif ( $words > 350 ) {
			$this->points[ $date_ymd ] *= Base::$points_config['content']['word-multipliers'][350];
		} elseif ( $words > 100 ) {
			$this->points[ $date_ymd ] *= Base::$points_config['content']['word-multipliers'][100];
		}

		$days = absint( Date::get_days_between_dates( $date, $this->get_date() ) );

		// Maximum range for awarded points is 30 days.
		if ( $days >= 30 ) {
			$this->points[ $date_ymd ] = 0;
			return $this->points[ $date_ymd ];
		}

		$this->points[ $date_ymd ] = ( $days < 7 )
			? round( $this->points[ $date_ymd ] ) // If the activity is new (less than 7 days old), award full points.
			: round( $this->points[ $date_ymd ] * max( 0, ( 1 - $days / 30 ) ) ); // Decay the points based on the age of the activity.

		return $this->points[ $date_ymd ];
	}
}
