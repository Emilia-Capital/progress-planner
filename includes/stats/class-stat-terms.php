<?php
/**
 * Stats about terms.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

/**
 * Stats about terms.
 */
class Stat_Terms extends Stat {

	/**
	 * The taxonomy for this stat.
	 *
	 * @var string
	 */
	protected $taxonomy = 'category';

	/**
	 * Set the taxonomy for this stat.
	 *
	 * @param string $taxonomy The taxonomy.
	 */
	public function set_taxonomy( $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}

	/**
	 * Get the stat data.
	 *
	 * @param string $period The period to get the data for.
	 *
	 * @return array
	 */
	public function get_data( $period = 'week' ) {
		return [
			'total' => (array) \wp_count_terms( [ 'taxonomy' => $this->taxonomy ] ),
		];
	}
}
