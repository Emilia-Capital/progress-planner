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
	 * Set the ID of the activity.
	 *
	 * @param int $id The ID of the activity.
	 *
	 * @return void
	 */
	public function set_id( int $id ) {
		$this->id = $id;
	}

	/**
	 * Get the ID of the activity.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set the date.
	 *
	 * @param \DateTime $date The date of the activity.
	 */
	public function set_date( \DateTime $date ) {
		$this->date = $date;
	}

	/**
	 * Get the date of the activity.
	 *
	 * @return \DateTime
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * Set the category.
	 *
	 * @param string $category The category of the activity.
	 */
	public function set_category( string $category ) {
		$this->category = $category;
	}

	/**
	 * Get the category of the activity.
	 *
	 * @return string
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * Set the type.
	 *
	 * @param string $type The type of the activity.
	 */
	public function set_type( string $type ) {
		$this->type = $type;
	}

	/**
	 * Get the type of the activity.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Set the data ID.
	 *
	 * @param int $data_id The data ID.
	 */
	public function set_data_id( int $data_id ) {
		$this->data_id = $data_id;
	}

	/**
	 * Get the data ID.
	 *
	 * @return int
	 */
	public function get_data_id() {
		return $this->data_id;
	}

	/**
	 * Set the user ID.
	 *
	 * @param int $user_id The user ID.
	 */
	public function set_user_id( int $user_id ) {
		$this->user_id = (int) $user_id;
	}

	/**
	 * Get the user ID.
	 *
	 * @return int
	 */
	public function get_user_id() {
		return (int) $this->user_id;
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
		} else {
			\progress_planner()->get_query()->insert_activity( $this );
		}
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
		$days = Date::get_days_between_dates( $date, $this->get_date() );
		return ( $days > 0 && $days < 7 )
			? 10
			: 10 * ( 1 - $days / 30 );
	}
}
