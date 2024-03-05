<?php
/**
 * Handle activities for Core, plugin, theme & translations updates.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Scan;

use ProgressPlanner\Activities\Maintenance as Activity_Maintenance;

/**
 * Handle activities for Core updates.
 */
class Maintenance {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	protected function register_hooks() {
		// Updates/Upgrades.
		\add_action( 'upgrader_process_complete', [ $this, 'on_upgrade' ], 10, 2 );

		// Deletions.
		\add_action( 'delete_plugin', [ $this, 'on_delete_plugin' ], 10, 2 );
		\add_action( 'delete_theme', [ $this, 'on_delete_plugin' ], 10, 2 );

		// Installations.
		\add_action( 'upgrader_process_complete', [ $this, 'on_install' ], 10, 2 );

		// Plugin activation & deactivation.
		\add_action( 'activated_plugin', [ $this, 'on_activate_plugin' ], 10 );
		\add_action( 'deactivated_plugin', [ $this, 'on_deactivate_plugin' ], 10 );

		// Theme switching.
		\add_action( 'switch_theme', [ $this, 'on_switch_theme' ], 10, 2 );
	}

	/**
	 * On install.
	 *
	 * @param \WP_Upgrader $upgrader The upgrader object.
	 * @param array        $options   The options.
	 *
	 * @return void
	 */
	public function on_install( $upgrader, $options ) {
		if ( 'install' !== $options['action'] ) {
			return;
		}
		$activity = new Activity_Maintenance();
		$activity->set_type( 'install_' . $this->get_install_type( $options ) );
		$activity->save();
	}

	/**
	 * On upgrade.
	 *
	 * @param \WP_Upgrader $upgrader The upgrader object.
	 * @param array        $options   The options.
	 *
	 * @return void
	 */
	public function on_upgrade( $upgrader, $options ) {
		if ( 'update' !== $options['action'] ) {
			return;
		}
		$activity = new Activity_Maintenance();
		$activity->set_type( 'update_' . $this->get_update_type( $options ) );
		$activity->save();
	}

	/**
	 * On delete plugin.
	 *
	 * @param string $plugin The plugin.
	 * @param bool   $deleted Whether the plugin was deleted.
	 *
	 * @return void
	 */
	public function on_delete_plugin( $plugin, $deleted ) {
		if ( ! $deleted ) {
			return;
		}

		$activity = new Activity_Maintenance();
		$activity->set_type( 'delete_plugin' );
		$activity->save();
	}

	/**
	 * On delete theme.
	 *
	 * @param string $theme The theme.
	 * @param bool   $deleted Whether the theme was deleted.
	 *
	 * @return void
	 */
	public function on_delete_theme( $theme, $deleted ) {
		if ( ! $deleted ) {
			return;
		}

		$activity = new Activity_Maintenance();
		$activity->set_type( 'delete_theme' );
		$activity->save();
	}

	/**
	 * On plugin activation.
	 *
	 * @return void
	 */
	public function on_activate_plugin() {
		$activity = new Activity_Maintenance();
		$activity->set_type( 'activate_plugin' );
		$activity->save();
	}

	/**
	 * On plugin deactivation.
	 *
	 * @return void
	 */
	public function on_deactivate_plugin() {
		$activity = new Activity_Maintenance();
		$activity->set_type( 'deactivate_plugin' );
		$activity->save();
	}

	/**
	 * On theme switch.
	 *
	 * @return void
	 */
	public function on_switch_theme() {
		$activity = new Activity_Maintenance();
		$activity->set_type( 'switch_theme' );
		$activity->save();
	}

	/**
	 * Get the type of the update.
	 *
	 * @param array $options The options.
	 *
	 * @return string
	 */
	protected function get_update_type( $options ) {
		return isset( $options['type'] ) ? $options['type'] : 'unknown';
	}
}
