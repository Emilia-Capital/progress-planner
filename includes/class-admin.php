<?php
/**
 * Create the admin page, menu etc.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Admin\Page;
use ProgressPlanner\Admin\Dashboard_Widget;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		new Page();
		new Dashboard_Widget();
	}
}
