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
	 * @param string $setting       The setting.
	 * @param mixed  $default_value The default value.
	 *
	 * @return mixed The value of the setting.
	 */
	public static function get( $setting, $default_value = null ) {
		self::load_settings();
		return self::$settings[ $setting ] ?? $default_value;
	}

	/**
	 * Set the value of a setting.
	 *
	 * @param string $setting The setting.
	 * @param mixed  $value   The value.
	 *
	 * @return void
	 */
	public static function set( $setting, $value ) {
		self::load_settings();
		self::$settings[ $setting ] = $value;
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
