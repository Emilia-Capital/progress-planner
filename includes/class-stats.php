<?php
/**
 * Stats class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Stats\Stat;
use ProgressPlanner\Stats\Stat_Posts;

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
	 * @param Stat   $stat The stat object.
	 *
	 * @return void
	 */
	public function add_stat( $id, Stat $stat ) {
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
	 * @return Stat
	 */
	public function get_stat( $id ): Stat {
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
}
