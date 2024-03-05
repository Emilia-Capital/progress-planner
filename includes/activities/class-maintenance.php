<?php
/**
 * Handle activities for maintenance activities.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

use ProgressPlanner\Activity;
use ProgressPlanner\Date;

/**
 * Handle activities for Core updates.
 */
class Maintenance extends Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	protected $category = 'maintenance';

	/**
	 * The data ID.
	 *
	 * This is not relevant for maintenance activities.
	 *
	 * @var int
	 */
	protected $data_id = 0;

	/**
	 * Save the activity.
	 *
	 * @return void
	 */
	public function save() {
		$this->set_date( new \DateTime() );
		$this->set_user_id( get_current_user_id() );

		parent::save();
	}

	/**
	 * Get the points for an activity.
	 *
	 * @param \DateTime $date The date for which we want to get the points of the activity.
	 *
	 * @return int
	 */
	public function get_points( $date ) {
		$points = \progress_planner()->get_dev_config( 'maintenance' );
		$days   = abs( Date::get_days_between_dates( $date, $this->get_date() ) );

		return ( $days < 7 ) ? $points : 0;
	}
}
