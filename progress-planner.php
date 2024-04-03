<?php
/**
 * A plugin to help you fight procrastination and get things done.
 *
 * @package ProgressPlanner
 *
 * Plugin name:       Progress Planner
 * Plugin URI:        https://progressplanner.com/
 * Description:       A plugin to help you fight procrastination and get things done.
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Version:           1.0.0
 * Author:            Joost de Valk
 * Author URI:        https://joost.blog/
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       progress-planner
 */

define( 'PROGRESS_PLANNER_DIR', __DIR__ );
define( 'PROGRESS_PLANNER_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

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
