<?php
/**
 * A recurring goal.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Goals;

/**
 * A recurring goal.
 */
class Goal_Recurring {

	/**
	 * The goal object.
	 *
	 * @var \ProgressPlanner\Goals\Goal
	 */
	private $goal;

	/**
	 * The goal frequency.
	 *
	 * @var string
	 */
	private $frequency;

	/**
	 * The start date.
	 *
	 * @var string
	 */
	private $start;

	/**
	 * The end date.
	 *
	 * @var string
	 */
	private $end;

	/**
	 * An array of occurences.
	 *
	 * @var array
	 */
	private $occurences = [];

	/**
	 * Constructor.
	 *
	 * @param \ProgressPlanner\Goals\Goal $goal      The goal object.
	 * @param string                      $frequency The goal frequency.
	 * @param string                      $start     The start date.
	 * @param string                      $end       The end date.
	 */
	public function __construct( $goal, $frequency, $start, $end ) {
		$this->goal      = $goal;
		$this->frequency = $frequency;
		$this->start     = $start;
		$this->end       = $end;
	}

	/**
	 * Build an array of occurences for the goal.
	 *
	 * @return Goal[]
	 */
	public function get_occurences() {
		if ( ! empty( $this->occurences ) ) {
			return $this->occurences;
		}

		$ranges = $this->get_date_periods();

		foreach ( $ranges as $range ) {
			$goal = clone $this->goal;
			$goal->set_start_date( $range['start'] );
			$goal->set_end_date( $range['end'] );
			$this->occurences[] = $goal;
		}

		return $this->occurences;
	}

	/**
	 * Get an array of periods with start and end dates.
	 *
	 * @return array
	 */
	public function get_date_periods() {
		$start = \DateTime::createFromFormat( 'Ymd', $this->start );
		$end   = \DateTime::createFromFormat( 'Ymd', $this->end );
		$end   = $end->modify( '+1 day' );

		switch ( $this->frequency ) {
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
			$dates_array[] = $date->format( 'Ymd' );
		}

		$date_ranges = [];
		foreach ( $dates_array as $key => $date ) {
			if ( isset( $dates_array[ $key + 1 ] ) ) {
				$date_ranges[] = [
					'start' => $date,
					'end'   => \DateTime::createFromFormat( 'Ymd', $dates_array[ $key + 1 ] )
						->modify( '-1 day' )
						->format( 'Ymd' ),
				];
			}
		}

		return $date_ranges;
	}
}
