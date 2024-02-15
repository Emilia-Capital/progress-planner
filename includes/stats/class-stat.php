<?php
/**
 * An object containing info about an individual stat.
 *
 * This object is meant to be extended by individual stat classes.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

/**
 * An object containing info about an individual stat.
 *
 * This object is meant to be extended by individual stat classes.
 */
class Stat {

	/**
	 * The setting name.
	 */
	const SETTING_NAME = 'progress_planner_stats';

	/**
	 * The stat type. This is used as a key in the settings array.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The stats setting value.
	 *
	 * @var array
	 */
	protected $stats;

	/**
	 * Get the value.
	 *
	 * @param string[]|int[] $index The index. This is an array of keys,
	 *                              which will be used to get the value.
	 *                              It will go over the array recursively,
	 *                              getting the value for the last key.
	 *                              See _wp_array_get for more info.
	 * @return mixed
	 */
	public function get_value( array $index = [] ) {
		if ( ! isset( $this->stats[ $this->type ] ) ) {
			$this->stats = \get_option( self::SETTING_NAME, [ $this->type => [] ] );
		}

		if ( ! empty( $index ) ) {
			return \_wp_array_get( $this->stats[ $this->type ], $index );
		}

		return $this->stats[ $this->type ];
	}

	/**
	 * Set the value.
	 *
	 * @param string[]|int[] $index The index. This is an array of keys,
	 *                              which will be used to set the value.
	 *                              It will go over the array recursively,
	 *                              updating the value for the last key.
	 *                              See _wp_array_set for more info.
	 * @param mixed          $value The value.
	 *
	 * @return bool
	 */
	public function set_value( array $index, $value ): bool {
		// Call $this->get_value, to populate $this->stats.
		$stats = \get_option( self::SETTING_NAME, [ $this->type => [] ] );

		// Add $this->type to the beginning of the index array.
		\array_unshift( $index, $this->type );

		// Update the value in the array.
		\_wp_array_set( $stats, $index, $value );

		// Save the option.
		$updated = \update_option( self::SETTING_NAME, $stats );
		$this->stats = $stats;

		return $updated;
	}
}
