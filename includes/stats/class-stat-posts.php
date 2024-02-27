<?php
/**
 * Stats about posts.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

use ProgressPlanner\Activities\Query;

/**
 * Stats about posts.
 */
class Stat_Posts {

	/**
	 * Get the value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return Query::get_instance()->query_activities(
			[
				'category' => 'post',
				'type'     => 'publish',
			]
		);
	}
}
