<?php
/**
 * Handle activities for Core, plugin, theme & translations updates.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Scan;

use ProgressPlanner\Activity;

/**
 * Handle activities for Core updates.
 */
class Maintenance extends Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	protected $category = 'maintenance';

	/**
	 * The data ID.
	 *
	 * This is not relevant for maintenance activities.
	 *
	 * @var int
	 */
	protected $data_id = 0;

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
		// Get the type of the update.
		$this->type = 'update_' . $this->get_update_type( $options );
		$this->save();
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

		$this->type = 'delete_plugin';
		$this->save();
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

		$this->type = 'delete_theme';
		$this->save();
	}

	/**
	 * On plugin activation.
	 *
	 * @return void
	 */
	public function on_activate_plugin() {
		$this->type = 'activate_plugin';
		$this->save();
	}

	/**
	 * On plugin deactivation.
	 *
	 * @return void
	 */
	public function on_deactivate_plugin() {
		$this->type = 'deactivate_plugin';
		$this->save();
	}

	/**
	 * On theme switch.
	 *
	 * @return void
	 */
	public function on_switch_theme() {
		$this->type = 'switch_theme';
		$this->save();
	}

	/**
	 * Save the activity.
	 *
	 * @return void
	 */
	public function save() {
		$this->set_date( new \DateTime() );
		$this->set_user_id( get_current_user_id() );

		parent::save();
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
