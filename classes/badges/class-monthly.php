<?php
/**
 * Badge object.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Badges;

/**
 * Badge class.
 */
final class Monthly extends Badge {

	/**
	 * The target points.
	 *
	 * @var int
	 */
	const TARGET_POINTS = 7;

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * An array of instances for this object (one/month).
	 *
	 * @var array
	 */
	protected static $instances = [];

	/**
	 * Contructor.
	 *
	 * @param string $id The badge ID.
	 */
	public function __construct( $id ) {
		$this->id = $id;
	}

	/**
	 * Get an array of instances (one for each month).
	 *
	 * @param int|null $year The year. If null, the current year is used.
	 *
	 * @return array
	 */
	public static function get_instances( $year = null ) {
		$year = $year ? (int) $year : gmdate( 'Y' );
		if ( ! isset( self::$instances[ $year ] ) ) {
			self::$instances[ $year ] = [];
		}

		if ( ! empty( self::$instances[ $year ] ) ) {
			return self::$instances[ $year ];
		}

		foreach ( array_keys( self::get_months() ) as $month ) {
			$id                         = 'monthly-' . $year . '-' . $month;
			self::$instances[ $year ][] = new self( $id );
		}

		return self::$instances[ $year ];
	}

	/**
	 * Get an array of months.
	 *
	 * @return array
	 */
	public static function get_months() {
		/*
		 * Indexed months, The array keys are prefixed with an "m"
		 * so that they are strings and not integers.
		 */
		$months = [
			'm1'  => __( 'Jack January', 'progress-planner' ),
			'm2'  => __( 'Felix February', 'progress-planner' ),
			'm3'  => __( 'Mary March', 'progress-planner' ),
			'm4'  => __( 'Avery April', 'progress-planner' ),
			'm5'  => __( 'Matteo May', 'progress-planner' ),
			'm6'  => __( 'Jasmine June', 'progress-planner' ),
			'm7'  => __( 'Joey July', 'progress-planner' ),
			'm8'  => __( 'Abed August', 'progress-planner' ),
			'm9'  => __( 'Sam September', 'progress-planner' ),
			'm10' => __( 'Oksana October', 'progress-planner' ),
			'm11' => __( 'Noah November', 'progress-planner' ),
			'm12' => __( 'Daisy December', 'progress-planner' ),
		];
		return $months;
	}

	/**
	 * The badge name.
	 *
	 * @return string
	 */
	public function get_name() {
		if ( ! $this->id ) {
			return '';
		}
		return self::get_months()[ 'm' . $this->get_month() ];
	}

	/**
	 * Get the badge description.
	 *
	 * @return string
	 */
	public function get_description() {
		return '';
	}

	/**
	 * Get the year for the month.
	 *
	 * @return string
	 */
	public function get_year() {
		return explode( '-', str_replace( 'monthly-', '', $this->id ) )[0];
	}

	/**
	 * Get the month for the badge.
	 *
	 * @return string
	 */
	public function get_month() {
		return str_replace( 'm', '', explode( '-', str_replace( 'monthly-', '', $this->id ) )[1] );
	}

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	public function progress_callback() {
		$saved_progress = $this->get_saved();

		// If we have a saved value, return it.
		if ( isset( $saved_progress['progress'] ) && isset( $saved_progress['remaining'] ) ) {
			return $saved_progress;
		}

		$month     = self::get_months()[ 'm' . $this->get_month() ];
		$year      = $this->get_year();
		$month_num = (int) $this->get_month();

		$start_date = \DateTime::createFromFormat( 'Y-m-d', "{$year}-{$month_num}-01" );
		$end_date   = \DateTime::createFromFormat( 'Y-m-d', "{$year}-{$month_num}-" . gmdate( 't', strtotime( $month ) ) );

		// Get the activities for the month.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category'   => 'suggested_task',
				'start_date' => $start_date,
				'end_date'   => $end_date,
			],
		);

		$points = 0;
		foreach ( $activities as $activity ) {
			$points += $activity->get_points( $activity->date );
		}

		if ( $points > self::TARGET_POINTS ) {
			$return_progress = [
				'progress'  => 100,
				'remaining' => 0,
			];
		} else {
			$return_progress = [
				'progress'  => (int) max( 0, min( 100, floor( 100 * $points / self::TARGET_POINTS ) ) ),
				'remaining' => self::TARGET_POINTS - $points,
			];
		}

		$this->save_progress( $return_progress );

		return $return_progress;
	}
}
