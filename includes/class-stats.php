<?php
/**
 * Stats class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Stats class.
 *
 * This is a collection of individual Stat objects.
 */
class Stats {

	/**
	 * The individual stats.
	 *
	 * @var array
	 */
	private $stats = array();

	/**
	 * Add a stat to the collection.
	 *
	 * @param string $id   The ID of the stat.
	 * @param Stat   $stat The stat object.
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
	 * @return Stat
	 */
	public function get_stat( $id ) {
		return $this->stats[ $id ];
	}
}
