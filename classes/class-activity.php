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
	protected $category;

	/**
	 * Type of the activity.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The date of the activity.
	 *
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * The data ID.
	 *
	 * Depending on the activity this is the post-ID, term-ID, comment-ID etc.
	 *
	 * @var int
	 */
	protected $data_id;

	/**
	 * ID of the activity.
	 *
	 * @var int
	 */
	protected $id;

	/**
	 * User ID.
	 *
	 * @var int
	 */
	protected $user_id;

	/**
	 * Activity points by date.
	 *
	 * @var array
	 */
	protected $points = [];

	/**
	 * Use the __get magic method to get the protected properties.
	 *
	 * @param string $name The name of the property.
	 */
	public function __get( $name ) {
		return $this->$name;
	}

	/**
	 * Use the __set magic method to set the protected properties.
	 *
	 * @param string $name  The name of the property.
	 * @param mixed  $value The value of the property.
	 */
	public function __set( $name, $value ) {
		$this->$name = $value;
	}

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
