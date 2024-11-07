<?php
/**
 * Handle activities for Core, plugin, theme & translations updates.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Actions;

/**
 * Handle activities for updates.
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
		\add_action( 'delete_plugin', [ $this, 'on_delete_plugin' ] );
		\add_action( 'delete_theme', [ $this, 'on_delete_plugin' ] );

		// Installations.
		\add_action( 'upgrader_process_complete', [ $this, 'on_install' ], 10, 2 );

		// Plugin activation & deactivation.
		\add_action( 'activated_plugin', [ $this, 'on_activate_plugin' ], 10 );
		\add_action( 'deactivated_plugin', [ $this, 'on_deactivate_plugin' ], 10 );

		// Theme switching.
		\add_action( 'switch_theme', [ $this, 'on_switch_theme' ] );
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
		$this->create_maintenance_activity( 'install_' . $this->get_install_type( $options ) );
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
		$this->create_maintenance_activity( 'update_' . $this->get_update_type( $options ) );
	}

	/**
	 * On delete plugin.
	 *
	 * @return void
	 */
	public function on_delete_plugin() {
		$this->create_maintenance_activity( 'delete_plugin' );
	}

	/**
	 * On delete theme.
	 *
	 * @return void
	 */
	public function on_delete_theme() {
		$this->create_maintenance_activity( 'delete_theme' );
	}

	/**
	 * On plugin activation.
	 *
	 * @return void
	 */
	public function on_activate_plugin() {
		$this->create_maintenance_activity( 'activate_plugin' );
	}

	/**
	 * On plugin deactivation.
	 *
	 * @return void
	 */
	public function on_deactivate_plugin() {
		$this->create_maintenance_activity( 'deactivate_plugin' );
	}

	/**
	 * On theme switch.
	 *
	 * @return void
	 */
	public function on_switch_theme() {
		$this->create_maintenance_activity( 'switch_theme' );
	}

	/**
	 * Create a new maintenance activity.
	 *
	 * @param string $type The type of the activity.
	 *
	 * @return void
	 */
	protected function create_maintenance_activity( $type ) {
		$activity       = new \Progress_Planner\Activities\Maintenance();
		$activity->type = $type;
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

	/**
	 * Get the type of the install.
	 *
	 * @param array $options The options.
	 *
	 * @return string
	 */
	protected function get_install_type( $options ) {
		return isset( $options['type'] ) ? $options['type'] : 'unknown';
	}
}
