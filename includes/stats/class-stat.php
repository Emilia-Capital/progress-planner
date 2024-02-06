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
	 * Get the stat data.
	 *
	 * @param string $period The period to get the data for.
	 *
	 * @return array
	 */
	abstract public function get_data( $period = 'week' );
}
