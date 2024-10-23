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
	 * @return array
	 */
	public static function get_instances() {
		if ( ! empty( self::$instances ) ) {
			return self::$instances;
		}

		foreach ( array_keys( self::get_months() ) as $month ) {
			self::$instances[] = new self( 'monthly-' . strtolower( $month ) );
		}

		return self::$instances;
	}

	/**
	 * Get an array of months.
	 *
	 * @return array
	 */
	public static function get_months() {
		$current_month = gmdate( 'm' );
		if ( $current_month >= 1 && $current_month <= 6 ) {
			return [
				'jan' => __( 'Jack January', 'progress-planner' ),
				'feb' => __( 'Felix February', 'progress-planner' ),
				'mar' => __( 'Mary March', 'progress-planner' ),
				'apr' => __( 'Avery April', 'progress-planner' ),
				'may' => __( 'Matteo May', 'progress-planner' ),
				'jun' => __( 'Jasmine June', 'progress-planner' ),
			];
		}
		return [
			'jul' => __( 'July', 'progress-planner' ),
			'aug' => __( 'August', 'progress-planner' ),
			'sep' => __( 'September', 'progress-planner' ),
			'oct' => __( 'October', 'progress-planner' ),
			'nov' => __( 'November', 'progress-planner' ),
			'dec' => __( 'December', 'progress-planner' ),
		];
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
		return self::get_months()[ $this->get_month() ];
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
	 * Get the month for the badge.
	 *
	 * @return string
	 */
	public function get_month() {
		return str_replace( 'monthly-', '', $this->id );
	}

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	public function progress_callback() {
		$month     = self::get_months()[ $this->get_month() ];
		$year      = $this->get_year( $month );
		$month_num = gmdate( 'm', strtotime( $month ) );

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
			return [
				'progress'  => 100,
				'remaining' => 0,
			];
		}

		return [
			'progress'  => (int) max( 0, min( 100, floor( 100 * $points / self::TARGET_POINTS ) ) ),
			'remaining' => self::TARGET_POINTS - $points,
		];
	}

	/**
	 * Get the year for the month.
	 *
	 * @param string $month The month.
	 * @return int
	 */
	public function get_year( $month ) {
		$current_month_num = gmdate( 'm' );
		$month_num         = gmdate( 'm', strtotime( $month ) );
		$year              = (int) gmdate( 'Y' );
		if ( $current_month_num < $month_num ) {
			return $year - 1;
		}
		return $year;
	}
}
