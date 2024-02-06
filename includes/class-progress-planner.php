<?php
/**
 * Progress Planner main plugin class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Main plugin class.
 */
class Progress_Planner {

	/**
	 * The Stats object.
	 *
	 * @var \ProgressPlanner\Stats
	 */
	private $stats;

	/**
	 * The Admin object.
	 *
	 * @var \ProgressPlanner\Admin
	 */
	private $admin;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->admin = new Admin();
		$this->stats = new Stats();
	}

	/**
	 * Get the stats object.
	 *
	 * @return \ProgressPlanner\Stats
	 */
	public function get_stats() {
		return $this->stats;
	}
}
