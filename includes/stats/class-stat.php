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
	 * The value.
	 *
	 * @var array
	 */
	protected $value;

	/**
	 * Date Query.
	 *
	 * The date query, which will be then passed-on to the WP_Date_Query object.
	 *
	 * @var array
	 */
	protected $date_query = [];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->value = $this->get_value();
	}

	/**
	 * Get the value.
	 *
	 * @param array $index The index. This is an array of keys, which will be used to get the value.
	 *                     This will go over the array recursively, getting the value for the last key.
	 *                     See _wp_array_get for more info.
	 * @return mixed
	 */
	public function get_value( $index = [] ) {
		if ( $this->value ) {
			return $this->value;
		}

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
	 * @param array $index The index. This is an array of keys, which will be used to set the value.
	 *                                This will go over the array recursively, updating the value for the last key.
	 *                                See _wp_array_set for more info.
	 * @param mixed $value The value.
	 */
	public function set_value( $index, $value ) {
		// Call $this->get_value, to populate $this->stats.
		$this->get_value();

		// Add $this->type to the beginning of the index array.
		\array_unshift( $index, $this->type );

		// Update the value in the array.
		\_wp_array_set( $this->stats, $index, $value );

		// Save the option.
		\update_option( self::SETTING_NAME, $this->stats );
	}

	/**
	 * Set the date query.
	 *
	 * @param array $date_query The date query.
	 */
	public function set_date_query( $date_query ) {
		$this->date_query = $date_query;
	}
}
