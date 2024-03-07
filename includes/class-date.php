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
	public static function get_range( $start, $end ) {
		$dates = iterator_to_array( new \DatePeriod( $start, new \DateInterval( 'P1D' ), $end ), false );
		return [
			'start' => $dates[0],
			'end'   => end( $dates ),
			'dates' => $dates,
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
	public static function get_periods( $start, $end, $frequency ) {
		$end->modify( '+1 day' );

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
				$date_ranges[] = static::get_range( $date, $period[ $key + 1 ] );
			}
		}
		if ( empty( $date_ranges ) ) {
			return [];
		}
		if ( $end->format( 'z' ) !== end( $date_ranges )['end']->format( 'z' ) ) {
			$date_ranges[] = static::get_range( end( $date_ranges )['end'], $end );
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

	/**
	 * Get start of week from a date.
	 *
	 * @param \DateTime $date The date.
	 *
	 * @return \DateTime
	 */
	public static function get_start_of_week( $date ) {
		$day_of_week = (int) $date->format( 'N' );
		$day_of_week = $day_of_week === 7 ? 0 : $day_of_week;
		return $date->modify( "-{$day_of_week} days" );
	}

	/**
	 * Get start of month from a date.
	 *
	 * @param \DateTime $date The date.
	 *
	 * @return \DateTime
	 */
	public static function get_start_of_month( $date ) {
		return $date->modify( 'first day of this month' );
	}

	/**
	 * Get number of days between two dates.
	 *
	 * @param \DateTime $date1 The first date.
	 * @param \DateTime $date2 The second date.
	 */
	public static function get_days_between_dates( $date1, $date2 ) {
		return (int) $date1->diff( $date2 )->format( '%R%a' );
	}
}
