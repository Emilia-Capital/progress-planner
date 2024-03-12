<?php
/**
 * Handle plugin settings.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Settings class.
 */
class Settings {

	/**
	 * The name of the settings option.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'progress_planner_settings';

	/**
	 * The settings.
	 *
	 * @var array
	 */
	private static $settings = [];

	/**
	 * Get the value of a setting.
	 *
	 * @param string|array $setting       The setting.
	 *                                    If a string, the name of the setting.
	 *                                    If an array, get value recursively from the settings.
	 *                                    See _wp_array_get() for more information.
	 * @param mixed        $default_value The default value.
	 *
	 * @return mixed The value of the setting.
	 */
	public static function get( $setting, $default_value = null ) {
		self::load_settings();

		// TODO: DELETE THIS PART. It's here for backward compatibility on test sites.
		if ( 'activation_date' === $setting ) {
			return \get_option( 'progress_planner_activation_date' );
		}

		if ( is_array( $setting ) ) {
			return \_wp_array_get( self::$settings, $setting, $default_value );
		}
		return self::$settings[ $setting ] ?? $default_value;
	}

	/**
	 * Set the value of a setting.
	 *
	 * @param string|array $setting The setting.
	 *                              If a string, the name of the setting.
	 *                              If an array, set value recursively in the settings.
	 *                              See _wp_array_set() for more information.
	 * @param mixed        $value   The value.
	 *
	 * @return void
	 */
	public static function set( $setting, $value ) {
		self::load_settings();
		if ( is_array( $setting ) ) {
			\_wp_array_set( self::$settings, $setting, $value );
		} else {
			self::$settings[ $setting ] = $value;
		}
		self::save_settings();
	}

	/**
	 * Load the settings.
	 *
	 * @return void
	 */
	private static function load_settings() {
		self::$settings = \get_option( self::OPTION_NAME, [] );
	}

	/**
	 * Save the settings.
	 *
	 * @return void
	 */
	private static function save_settings() {
		\update_option( self::OPTION_NAME, self::$settings, false );
	}

	/**
	 * Delete a setting.
	 *
	 * @param string $setting The setting.
	 *
	 * @return void
	 */
	public static function delete( $setting ) {
		self::load_settings();
		unset( self::$settings[ $setting ] );
		self::save_settings();
	}

	/**
	 * Delete all settings.
	 *
	 * @return void
	 */
	public static function delete_all() {
		self::$settings = [];
		self::save_settings();
	}
}
