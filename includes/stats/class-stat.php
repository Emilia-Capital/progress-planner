<?php
/**
 * An object containing info about an individual stat.
 *
 * This is an abstract class, meant to be extended by individual stat classes.
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
	 * @return array
	 */
	abstract public function get_data();
}
