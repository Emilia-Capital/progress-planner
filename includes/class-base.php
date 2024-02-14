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
class Base {

	/**
	 * An instance of this class.
	 *
	 * @var \ProgressPlanner\Base
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
	 * @return \ProgressPlanner\Base
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
		$this->goals = new Goals();
	}

	/**
	 * Get the stats object.
	 *
	 * @return \ProgressPlanner\Stats
	 */
	public function get_stats() {
		return $this->stats;
	}

	/**
	 * Get the admin object.
	 *
	 * @return \ProgressPlanner\Admin
	 */
	public function get_admin() {
		return $this->admin;
	}
}
