<?php
/**
 * Progress Planner main plugin class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Activities\Activity_Post;
use ProgressPlanner\Admin;

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

		( new Activity_Post() )->register_hooks();
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
