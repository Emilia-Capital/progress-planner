<?php
/**
 * Create the admin page, menu etc.
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
}
