<?php
/**
 * Activity class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

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
	 * @var string
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
		global $progress_planner;
		$existing = $progress_planner->get_query()->get_instance()->query_activities(
			[
				'category' => $this->category,
				'type'     => $this->type,
				'data_id'  => (string) $this->data_id,
			],
			'RAW'
		);
		if ( ! empty( $existing ) ) {
			$progress_planner->get_query()->update_activity( $existing[0]->id, $this );
			return;
		}
		$progress_planner->get_query()->insert_activity( $this );
		\do_action( 'progress_planner_activity_saved', $this );
	}

	/**
	 * Delete the activity.
	 *
	 * @return void
	 */
	public function delete() {
		global $progress_planner;
		$progress_planner->get_query()->delete_activity( $this );
		\do_action( 'progress_planner_activity_deleted', $this );
	}

	/**
	 * Get the points for an activity.
	 *
	 * @param \DateTime $date The date for which we want to get the points of the activity.
	 *
	 * @return int
	 */
	public function get_points( $date ) {
		global $progress_planner;
		$date_ymd = $date->format( 'Ymd' );
		if ( isset( $this->points[ $date_ymd ] ) ) {
			return $this->points[ $date_ymd ];
		}
		$days = abs( $progress_planner->get_date()->get_days_between_dates( $date, $this->date ) );

		// Default points.
		$default_points = 10;
		if ( isset( Base::$points_config[ $this->category ][ $this->type ] ) ) {
			$default_points = Base::$points_config[ $this->category ][ $this->type ];
		} elseif ( isset( Base::$points_config[ $this->category ]['default'] ) ) {
			$default_points = Base::$points_config[ $this->category ]['default'];
		} elseif ( isset( Base::$points_config[ $this->category ] ) && \is_int( Base::$points_config[ $this->category ] ) ) {
			$default_points = Base::$points_config[ $this->category ];
		}

		$this->points[ $date_ymd ] = ( $days < 7 )
			? $default_points
			: round( $default_points * max( 0, ( 1 - $days / 30 ) ) );

		return $this->points[ $date_ymd ];
	}
}
