<?php
/**
 * Plugin name: Progress Planner
 */

define( 'PROGRESS_PLANNER_DIR', __DIR__ );

require_once PROGRESS_PLANNER_DIR . '/includes/autoload.php';

\ProgressPlanner\Progress_Planner::get_instance();
