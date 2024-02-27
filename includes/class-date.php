<?php
/**
 * Helper object for date-related functions.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Date class.
 */
class Date {

	/**
	 * Get a range of dates.
	 *
	 * @param string|int $start The start date.
	 * @param string|int $end   The end date.
	 *
	 * @return array [
	 *                 'start' => 'Ymd',
	 *                 'end'   => 'Ymd',
	 *                 'dates' => [ 'Ymd', 'Ymd', ... ],
	 *               ].
	 */
	public function get_range( $start, $end ) {
		return [
			'start' => $start,
			'end'   => $end,
			'dates' => iterator_to_array( new \DatePeriod( $start, new \DateInterval( 'P1D' ), $end ), false ),
		];
	}

	/**
	 * Get an array of periods with start and end dates.
	 *
	 * @param int|string $start     The start date.
	 * @param int|string $end       The end date.
	 * @param string     $frequency The frequency. Can be 'daily', 'weekly', 'monthly'.
	 *
	 * @return array
	 */
	public function get_periods( $start, $end, $frequency ) {
		$end = $end->modify( '+1 day' );

		switch ( $frequency ) {
			case 'daily':
				$interval = new \DateInterval( 'P1D' );
				break;

			case 'monthly':
				$interval = new \DateInterval( 'P1M' );
				break;

			default: // Default to weekly.
				$interval = new \DateInterval( 'P1W' );
				// Make sure we start and end on a Monday.
				$start->modify( 'last Monday' );
				$end->modify( 'next Monday' );
				break;
		}

		$period = iterator_to_array( new \DatePeriod( $start, $interval, $end ), false );

		$date_ranges = [];
		foreach ( $period as $key => $date ) {
			if ( isset( $period[ $key + 1 ] ) ) {
				$date_ranges[] = $this->get_range( $date, $period[ $key + 1 ] );
			}
		}

		return $date_ranges;
	}

	/**
	 * Get DateTime object from a mysql date.
	 *
	 * @param string $date The date.
	 *
	 * @return \DateTime
	 */
	public static function get_datetime_from_mysql_date( $date ) {
		return \DateTime::createFromFormat( 'U', (int) mysql2date( 'U', $date ) );
	}
}
