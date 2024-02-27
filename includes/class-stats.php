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
}
