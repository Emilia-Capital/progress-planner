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
	 * @param string $start The start date.
	 * @param string $end   The end date.
	 *
	 * @return array [
	 *                 'start' => 'Ymd',
	 *                 'end'   => 'Ymd',
	 *                 'dates' => [ 'Ymd', 'Ymd', ... ],
	 *               ].
	 */
	public function get_range( $start, $end ) {
		$start = \DateTime::createFromFormat( $this->format, $start );
		$end   = \DateTime::createFromFormat( $this->format, $end );

		$dates = [];
		$range = new \DatePeriod( $start, new \DateInterval( 'P1D' ), $end );
		foreach ( $range as $date ) {
			$dates[] = $date->format( $this->format );
		}

		return [
			'start' => $start->format( $this->format ),
			'end'   => $end->format( $this->format ),
			'dates' => $dates,
		];
	}

	/**
	 * Get an array of periods with start and end dates.
	 *
	 * @param string $start     The start date.
	 * @param string $end       The end date.
	 * @param string $frequency The frequency. Can be 'daily', 'weekly', 'monthly'.
	 *
	 * @return array
	 */
	public function get_periods( $start, $end, $frequency ) {
		$start = \DateTime::createFromFormat( $this->format, $start );
		$end   = \DateTime::createFromFormat( $this->format, $end );
		$end   = $end->modify( '+1 day' );

		switch ( $frequency ) {
			case 'daily':
				$interval = new \DateInterval( 'P1D' );
				break;

			case 'weekly':
				$interval = new \DateInterval( 'P1W' );
				break;

			case 'monthly':
				$interval = new \DateInterval( 'P1M' );
				break;
		}

		$period      = new \DatePeriod( $start, $interval, 100 );
		$dates_array = [];
		foreach ( $period as $date ) {
			$dates_array[] = $date->format( $this->format );
		}

		$date_ranges = [];
		foreach ( $dates_array as $key => $date ) {
			if ( isset( $dates_array[ $key + 1 ] ) ) {
				$date_ranges[] = $this->get_range(
					$date,
					\DateTime::createFromFormat( $this->format, $dates_array[ $key + 1 ] )
				);
			}
		}

		return $date_ranges;
	}
}
