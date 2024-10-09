<?php
/**
 * Handle activities for maintenance activities.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Activities;

use Progress_Planner\Activity;
use Progress_Planner\Date;
use Progress_Planner\Base;

/**
 * Handle activities for Core updates.
 */
class Maintenance extends Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	public $category = 'maintenance';

	/**
	 * The data ID.
	 *
	 * This is not relevant for maintenance activities.
	 *
	 * @var int
	 */
	public $data_id = 0;

	/**
	 * Save the activity.
	 *
	 * @return void
	 */
	public function save() {
		$this->date    = new \DateTime();
		$this->user_id = \get_current_user_id();

		$existing = \progress_planner()->get_query()->query_activities(
			[
				'category'   => $this->category,
				'type'       => $this->type,
				'data_id'    => $this->data_id,
				'start_date' => $this->date,
			],
			'RAW'
		);
		if ( ! empty( $existing ) ) {
			\progress_planner()->get_query()->update_activity( $existing[0]->id, $this );
			return;
		}
		\progress_planner()->get_query()->insert_activity( $this );
		\do_action( 'progress_planner_activity_saved', $this );
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
		$this->points[ $date_ymd ] = Base::$points_config['maintenance'];
		$days                      = abs( Date::get_days_between_dates( $date, $this->date ) );

		$this->points[ $date_ymd ] = ( $days < 7 ) ? $this->points[ $date_ymd ] : 0;

		return $this->points[ $date_ymd ];
	}
}
