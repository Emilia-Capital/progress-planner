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
			$id                = 'monthly-' . gmdate( 'Y' ) . '-' . str_replace( '-', '', $month );
			self::$instances[] = new self( $id );
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
		$months        = [
			// Indexed months, The array keys are prefixed with a dash
			// so that they are strings and not integers.
			'-1'  => __( 'Jack January', 'progress-planner' ),
			'-2'  => __( 'Felix February', 'progress-planner' ),
			'-3'  => __( 'Mary March', 'progress-planner' ),
			'-4'  => __( 'Avery April', 'progress-planner' ),
			'-5'  => __( 'Matteo May', 'progress-planner' ),
			'-6'  => __( 'Jasmine June', 'progress-planner' ),
			'-7'  => __( 'July', 'progress-planner' ),
			'-8'  => __( 'August', 'progress-planner' ),
			'-9'  => __( 'September', 'progress-planner' ),
			'-10' => __( 'October', 'progress-planner' ),
			'-11' => __( 'November', 'progress-planner' ),
			'-12' => __( 'December', 'progress-planner' ),
		];
		return ( $current_month >= 1 && $current_month <= 6 )
			? array_slice( $months, 0, 6 )
			: array_slice( $months, -6 );
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
		return explode( '-', str_replace( 'monthly-', '', $this->id ) )[1];
	}

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	public function progress_callback() {
		$month     = self::get_months()[ $this->get_month() ];
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
	 * @return string
	 */
	public function get_year() {
		return explode( '-', str_replace( 'monthly-', '', $this->id ) )[0];
	}

	/**
	 * Print the icon.
	 *
	 * @return array
	 */
	public function get_icons_paths() {
		// Icons are named "YEAR-MONTH.svg".
		return [
			"images/badges/monthly/{$this->get_year()}-{$this->get_month()}.svg",
			"images/badges/monthly/{$this->get_year()}-{$this->get_month()}-gray.svg",
		];
	}
}
