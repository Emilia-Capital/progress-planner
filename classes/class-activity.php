<?php
/**
 * Activity class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Date;

/**
 * Activity class.
 */
class Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	public $category;

	/**
	 * Type of the activity.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * The date of the activity.
	 *
	 * @var \DateTime
	 */
	public $date;

	/**
	 * The data ID.
	 *
	 * Depending on the activity this is the post-ID, term-ID, comment-ID etc.
	 *
	 * @var int
	 */
	public $data_id;

	/**
	 * ID of the activity.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * User ID.
	 *
	 * @var int
	 */
	public $user_id;

	/**
	 * Activity points by date.
	 *
	 * @var array
	 */
	public $points = [];

	/**
	 * Save the activity.
	 *
	 * @return void
	 */
	public function save() {
		$existing = \progress_planner()->get_query()->query_activities(
			[
				'category' => $this->category,
				'type'     => $this->type,
				'data_id'  => $this->data_id,
			],
			'RAW'
		);
		if ( ! empty( $existing ) ) {
			\progress_planner()->get_query()->update_activity( $existing[0]->id, $this );
			return;
		}
		\progress_planner()->get_query()->insert_activity( $this );
	}

	/**
	 * Delete the activity.
	 *
	 * @return void
	 */
	public function delete() {
		\progress_planner()->get_query()->delete_activity( $this );
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
		$days                      = abs( Date::get_days_between_dates( $date, $this->date ) );
		$this->points[ $date_ymd ] = ( $days < 7 )
			? 10
			: round( 10 * max( 0, ( 1 - $days / 30 ) ) );

		return $this->points[ $date_ymd ];
	}
}
