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
	 * Date format.
	 *
	 * @var string
	 */
	const FORMAT = 'Ymd';

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
		$start = \DateTime::createFromFormat( self::FORMAT, $start );
		$end   = \DateTime::createFromFormat( self::FORMAT, $end );

		$dates = [];
		$range = new \DatePeriod( $start, new \DateInterval( 'P1D' ), $end );
		foreach ( $range as $date ) {
			$dates[] = $date->format( self::FORMAT );
		}

		return [
			'start' => $start->format( self::FORMAT ),
			'end'   => $end->format( self::FORMAT ),
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
	public function get_periods( $start, $end, $frequency ) {
		$start = \DateTime::createFromFormat( self::FORMAT, $start );
		$end   = \DateTime::createFromFormat( self::FORMAT, $end );
		$end   = $end->modify( '+1 day' );

		switch ( $frequency ) {
			case 'daily':
				$interval = new \DateInterval( 'P1D' );
				break;

			case 'monthly':
				$interval = new \DateInterval( 'P1M' );
				break;

			default: // Default to weekly.
				$interval = new \DateInterval( 'P1W' );
				break;
		}

		$period      = new \DatePeriod( $start, $interval, 100 );
		$dates_array = [];
		foreach ( $period as $date ) {
			$dates_array[] = $date->format( self::FORMAT );
		}

		$date_ranges = [];
		foreach ( $dates_array as $key => $date ) {
			if ( isset( $dates_array[ $key + 1 ] ) ) {
				$datetime = \DateTime::createFromFormat( self::FORMAT, $dates_array[ $key + 1 ] );
				if ( ! $datetime ) {
					continue;
				}
				$date_ranges[] = $this->get_range(
					$date,
					$datetime->format( self::FORMAT )
				);
			}
		}

		return $date_ranges;
	}
}
