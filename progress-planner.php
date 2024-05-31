<?php
/**
 * A plugin to help you fight procrastination and get things done.
 *
 * @package Progress_Planner
 *
 * Plugin name:       Progress Planner
 * Plugin URI:        https://progressplanner.com/
 * Description:       Track, motivate, and enhance your website management with daily activity tracking and weekly progress reports.
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Version:           0.9
 * Author:            Team Emilia Projects
 * Author URI:        https://emilia.capital/
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       progress-planner
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PROGRESS_PLANNER_DIR', __DIR__ );
define( 'PROGRESS_PLANNER_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Autoload classes.
 */
spl_autoload_register(
	function ( $class_name ) {
		$prefix = 'Progress_Planner\\';

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
 * @return \Progress_Planner\Base
 */
function progress_planner() {
	return \Progress_Planner\Base::get_instance();
}

progress_planner();
