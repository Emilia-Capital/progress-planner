<?php
/**
 * Stats class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Stats\Stat_Posts;

/**
 * Stats class.
 *
 * This is a collection of objects.
 */
class Stats {

	/**
	 * The individual stats.
	 *
	 * @var array
	 */
	private $stats = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_stats();
	}

	/**
	 * Add a stat to the collection.
	 *
	 * @param string $id   The ID of the stat.
	 * @param Object $stat The stat object.
	 *
	 * @return void
	 */
	public function add_stat( $id, $stat ) {
		$this->stats[ $id ] = $stat;
	}

	/**
	 * Get all stats.
	 *
	 * @return array
	 */
	public function get_all_stats() {
		return $this->stats;
	}

	/**
	 * Get an individual stat.
	 *
	 * @param string $id The ID of the stat.
	 *
	 * @return Object
	 */
	public function get_stat( $id ) {
		return $this->stats[ $id ];
	}

	/**
	 * Register the individual stats.
	 *
	 * @return void
	 */
	private function register_stats() {
		$this->add_stat( 'posts', new Stat_Posts() );
	}

	/**
	 * Get number of activities for date range.
	 *
	 * @param \DateTime $start_date The start date.
	 * @param \DateTime $end_date   The end date.
	 * @param array     $args       The query arguments.
	 *
	 * @return int
	 */
	public function get_number_of_activities( $start_date, $end_date, $args = [] ) {
		$args['start_date'] = $start_date;
		$args['end_date']   = $end_date;
		$activities = Query::get_instance()->query_activities( $args );
		return count( $activities );
	}
}
