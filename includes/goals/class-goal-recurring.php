<?php
/**
 * A recurring goal.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Goals;

use ProgressPlanner\Date;

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
	 * @var int|string
	 */
	private $start;

	/**
	 * The end date.
	 *
	 * @var int|string
	 */
	private $end;

	/**
	 * An array of occurences.
	 *
	 * @var Goal[]
	 */
	private $occurences = [];

	/**
	 * Constructor.
	 *
	 * @param \ProgressPlanner\Goals\Goal $goal      The goal object.
	 * @param string                      $frequency The goal frequency.
	 * @param int|string                  $start     The start date.
	 * @param int|string                  $end       The end date.
	 */
	public function __construct( $goal, $frequency, $start, $end ) {
		$this->goal      = $goal;
		$this->frequency = $frequency;
		$this->start     = $start;
		$this->end       = $end;
	}

	/**
	 * Get the goal title.
	 *
	 * @return string
	 */
	public function get_goal() {
		return $this->goal;
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
		$date   = new Date();
		$ranges = $date->get_periods( $this->start, $this->end, $this->frequency );

		// If the last range ends before today, add a new range.
		if ( (int) gmdate( 'Ymd' ) > (int) end( $ranges )['end']->format( 'Ymd' ) ) {
			$ranges[] = $date->get_range(
				end( $ranges )['end'],
				new \DateTime( 'tomorrow' )
			);
		}

		foreach ( $ranges as $range ) {
			$goal = clone $this->goal;
			$goal->set_start_date( $range['start'] );
			$goal->set_end_date( $range['end'] );
			$this->occurences[] = $goal;
		}

		return $this->occurences;
	}
}
