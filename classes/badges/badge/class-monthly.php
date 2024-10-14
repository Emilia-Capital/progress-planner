<?php
/**
 * Badge object.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Badges\Badge;

use Progress_Planner\Badges\Badge;

/**
 * Badge class.
 */
final class Monthly extends Badge {

	/**
	 * The target points.
	 *
	 * @var int
	 */
	const TARGET_POINTS = 100;

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
	 * Get an array of instances (one for each month).
	 *
	 * @return array
	 */
	public static function get_instances() {
		if ( ! empty( self::$instances ) ) {
			return self::$instances;
		}
		$instances = [];
		foreach ( array_keys( self::get_months() ) as $month ) {
			$instances[ $month ]     = new self();
			$instances[ $month ]->id = 'monthly-' . strtolower( $month );
			$instances[ $month ]->progress_callback();
		}

		// Reorder the instances so that the current month is first.
		$current_month  = (int) gmdate( 'm' ) - 1;
		$months_keys    = array_keys( $instances );
		$ordered_months = array_merge(
			array_slice( $months_keys, $current_month ),
			array_slice( $months_keys, 0, $current_month )
		);
		// Reverse the array so that the current month is last.
		$ordered_months    = array_reverse( $ordered_months );
		$ordered_instances = [];
		foreach ( $ordered_months as $month ) {
			$ordered_instances[ $month ] = $instances[ $month ];
		}
		// Pull the last item of the array, and put it in the beginning.
		$last = array_pop( $ordered_instances );
		array_unshift( $ordered_instances, $last );

		self::$instances = $ordered_instances;
		return self::$instances;
	}

	/**
	 * Get an array of months.
	 *
	 * @return array
	 */
	public static function get_months() {
		return [
			'jan' => __( 'January', 'progress-planner' ),
			'feb' => __( 'February', 'progress-planner' ),
			'mar' => __( 'March', 'progress-planner' ),
			'apr' => __( 'April', 'progress-planner' ),
			'may' => __( 'May', 'progress-planner' ),
			'jun' => __( 'June', 'progress-planner' ),
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
		$month = str_replace( 'monthly-', '', $this->id );
		return self::get_months()[ $month ] . ' ' . $this->get_year( $month );
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
	 * The badge icons.
	 *
	 * @return array
	 */
	public function get_icons_svg() {
		// TODO: Add the badge icons.
		return [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge2_gray.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge2_gray.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/writing_badge2.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/writing_badge2.svg',
			],
		];
	}

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	public function progress_callback() {
		$month           = self::get_months()[ str_replace( 'monthly-', '', $this->id ) ];
		$year            = $this->get_year( $month );
		$is_current_year = (int) gmdate( 'Y' ) === $year;
		$month_num       = gmdate( 'm', strtotime( $month ) );

		$start_date = \DateTime::createFromFormat( 'Y-m-d', "{$year}-{$month_num}-01" );
		if ( $is_current_year ) {
			$end_date = \DateTime::createFromFormat( 'Y-m-d', "{$year}-{$month_num}-" . gmdate( 't', strtotime( $month ) ) );
		} else {
			$is_leap_year = gmdate( 'L', strtotime( "{$year}-01-01" ) );
			$day          = (int) gmdate( 't', strtotime( $month ) ) - ( 1 === (int) $is_leap_year && 2 === (int) $month_num ? 1 : 0 );
			$end_date     = \DateTime::createFromFormat(
				'Y-m-d',
				"{$year}-{$month_num}-{$day}"
			);
		}

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
