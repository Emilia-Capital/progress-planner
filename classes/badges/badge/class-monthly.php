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
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id = 'monthly';

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
		foreach ( array_keys( self::get_months() ) as $month ) {
			self::$instances[ $month ]     = new self();
			self::$instances[ $month ]->id = 'monthly-' . strtolower( $month );
			self::$instances[ $month ]->progress_callback();
		}

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
		$month = str_replace( 'monthly-', '', $this->id );
		return sprintf(
			/* translators: %s: The month name. */
			esc_html__( 'Monthly: %s', 'progress-planner' ),
			self::get_months()[ $month ]
		);
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
		$month             = self::get_months()[ str_replace( 'monthly-', '', $this->id ) ];
		$month_num         = gmdate( 'm', strtotime( $month ) );
		$current_month_num = gmdate( 'm' );
		$is_current_year   = true;
		$year              = gmdate( 'Y' );
		if ( $current_month_num < $month_num ) {
			$is_current_year = false;
			$year            = (int) gmdate( 'Y' ) - 1;
		}

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
				'category'   => 'content',
				'type'       => 'publish',
				'start_date' => $start_date,
				'end_date'   => $end_date,
			],
		);

		$points = 0;
		foreach ( $activities as $activity ) {
			$points += $activity->get_points( $activity->date );
		}

		return [];
	}
}
