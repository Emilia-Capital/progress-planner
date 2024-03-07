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
	 * The number of breaks in the streak that are allowed.
	 *
	 * @var int
	 */
	private $allowed_break = 0;

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
	 * @param \DateTime                   $start     The start date.
	 * @param \DateTime                   $end       The end date.
	 * @param int                         $allowed_break The number of breaks in the streak that are allowed.
	 */
	public function __construct( $goal, $frequency, $start, $end, $allowed_break = 0 ) {
		$this->goal          = $goal;
		$this->frequency     = $frequency;
		$this->start         = $start;
		$this->end           = $end;
		$this->allowed_break = $allowed_break;
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

	/**
	 * Get the streak for weekly posts.
	 *
	 * @return int The number of weeks for this streak.
	 */
	public function get_streak() {
		// Bail early if there is no goal.
		if ( ! $this ) {
			return [
				'number'      => 0,
				'title'       => $this->get_goal()->get_details()['title'],
				'description' => $this->get_goal()->get_details()['description'],
			];
		}

		// Reverse the order of the occurences.
		$occurences = $this->get_occurences();

		// Calculate the streak number.
		$streak_nr  = 0;
		$max_streak = 0;
		foreach ( $occurences as $occurence ) {
			/**
			 * Evaluate the occurence.
			 * If the occurence is true, then increment the streak number.
			 * Otherwise, reset the streak number.
			 */
			$evaluation = $occurence->evaluate();
			if ( $evaluation ) {
				++$streak_nr;
				$max_streak = max( $max_streak, $streak_nr );
				continue;
			}

			if ( $this->allowed_break > 0 ) {
				--$this->allowed_break;
				continue;
			}

			$streak_nr = 0;
		}

		return [
			'max_streak'     => $max_streak,
			'current_streak' => $streak_nr,
			'title'          => $this->get_goal()->get_details()['title'],
			'description'    => $this->get_goal()->get_details()['description'],
		];
	}
}
