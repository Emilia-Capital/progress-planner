<?php
/**
 * An object containing info about an individual stat.
 *
 * This is an abstract class, meant to be extended by individual stat classes.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

/**
 * An object containing info about an individual stat.
 *
 * This is an abstract class, meant to be extended by individual stat classes.
 */
abstract class Stat {

	/**
	 * Date Query.
	 *
	 * The date query, which will be then passed-on to the WP_Date_Query object.
	 *
	 * @var array
	 */
	protected $date_query = [];

	/**
	 * Set the date query.
	 *
	 * @param array $date_query The date query.
	 *
	 * @return Stat Returns this object to allow chaining methods.
	 */
	public function set_date_query( $date_query ) {
		$this->date_query = $date_query;
		return $this;
	}

	/**
	 * Get the stat data.
	 *
	 * @return array
	 */
	abstract public function get_data();
}
