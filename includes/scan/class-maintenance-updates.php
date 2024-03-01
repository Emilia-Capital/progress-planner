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
class Maintenance_Updates extends Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	protected $category = 'maintenance';

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
		\add_action( 'upgrader_process_complete', [ $this, 'on_upgrade' ], 10, 2 );
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
		// Get the type of the update.
		$this->type = $this->get_update_type( $options );
		$this->set_date( new \DateTime() );
		$this->set_data_id( 0 );
		$this->set_user_id( get_current_user_id() );
		$this->save();
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
