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
	 * An instance of this class.
	 *
	 * @var \ProgressPlanner\Progress_Planner
	 */
	private static $instance;

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
	 * Get the single instance of this class.
	 *
	 * @return \ProgressPlanner\Progress_Planner
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
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
