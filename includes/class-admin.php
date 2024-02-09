<?php
/**
 * Create the admin page, menu etc.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Admin page opbject.
	 *
	 * @var \ProgressPlanner\Admin\Page
	 */
	private $admin_page;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->admin_page = new Admin\Page();
	}

	/**
	 * Get the admin page object.
	 *
	 * @return \ProgressPlanner\Admin\Page
	 */
	public function get_admin_page() {
		return $this->admin_page;
	}
}
