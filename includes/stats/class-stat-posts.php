<?php
/**
 * Stats about posts.
 */

namespace ProgressPlanner\Stats;

/**
 * Stats about posts.
 */
class Stat_Posts extends Stat {

	/**
	 * Get the stat data.
	 *
	 * @return array
	 */
	public function get_data() {
		return array(
			'counts' => (array) wp_count_posts(),
		);
	}
}
