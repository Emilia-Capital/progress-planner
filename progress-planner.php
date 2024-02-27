<?php
/**
 * Plugin name: Progress Planner
 *
 * @package ProgressPlanner
 */

define( 'PROGRESS_PLANNER_DIR', __DIR__ );
define( 'PROGRESS_PLANNER_URL', plugin_dir_url( __FILE__ ) );

require_once PROGRESS_PLANNER_DIR . '/includes/autoload.php';

/**
 * Get the progress planner instance.
 *
 * @return \ProgressPlanner\Base
 */
function progress_planner() {
	return \ProgressPlanner\Base::get_instance();
}

progress_planner();
