<?php
/**
 * An object containing info about an individual goal.
 *
 * This object is meant to be extended by individual goal classes.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Goals;

/**
 * An object containing info about an individual goal.
 */
class Goal_Posts extends Goal {

	/**
	 * Whether this goal has been accomplished or not.
	 *
	 * @return bool
	 */
	public function evaluate() {
		$callback = $this->get_details()['evaluate'];
		return $callback( $this );
	}
}
