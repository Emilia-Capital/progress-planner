<?php
/**
 * Plugin name: Progress Planner
 *
 * @package ProgressPlanner
 */

define( 'PROGRESS_PLANNER_DIR', __DIR__ );
define( 'PROGRESS_PLANNER_URL', plugin_dir_url( __FILE__ ) );

require_once PROGRESS_PLANNER_DIR . '/includes/autoload.php';

\ProgressPlanner\Base::get_instance();
