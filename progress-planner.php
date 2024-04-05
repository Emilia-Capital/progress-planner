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

/**
 * Autoload classes.
 */
spl_autoload_register(
	function ( $class_name ) {
		$prefix = 'ProgressPlanner\\';

		if ( 0 !== \strpos( $class_name, $prefix ) ) {
			return;
		}

		$class_name = \str_replace( $prefix, '', $class_name );

		$parts = \explode( '\\', $class_name );
		$file  = PROGRESS_PLANNER_DIR . '/classes/';
		$last  = \array_pop( $parts );

		foreach ( $parts as $part ) {
			$file .= strtolower( $part ) . '/';
		}
		$file .= 'class-' . \str_replace( '_', '-', \strtolower( $last ) ) . '.php';

		if ( \file_exists( $file ) ) {
			require_once $file;
		}
	}
);

/**
 * Get the progress planner instance.
 *
 * @return \ProgressPlanner\Base
 */
function progress_planner() {
	return \ProgressPlanner\Base::get_instance();
}

progress_planner();
