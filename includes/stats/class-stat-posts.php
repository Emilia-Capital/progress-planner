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
	 * The post-type for this stat.
	 *
	 * @var string
	 */
	protected $post_type = 'post';

	/**
	 * Set the post-type for this stat.
	 *
	 * @param string $post_type The post-type.
	 */
	public function set_post_type( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * Get the stat data.
	 *
	 * @return array
	 */
	public function get_data() {
		return array(
			'total' => (array) \wp_count_posts(),
			'day'   => $this->get_posts_stats_by_date( [
				[
					'after'     => 'today',
					'inclusive' => true,
				],
			] ),
			'week'  => $this->get_posts_stats_by_date( [
				[
					'after'     => '-1 week',
					'inclusive' => true,
				],
			] ),
			'month' => $this->get_posts_stats_by_date( [
				[
					'after'     => '-1 month',
					'inclusive' => true,
				],
			] ),
			'year'  => $this->get_posts_stats_by_date( [
				[
					'after'     => '-1 year',
					'inclusive' => true,
				],
			] ),
		);
	}

	/**
	 * Get posts by dates.
	 *
	 * @param array $date_query The date query.
	 * @return array
	 */
	private function get_posts_stats_by_date( $date_query ) {
		$args = array(
			'posts_per_page'  => 1000,
			'post_type'       => $this->post_type,
			'post_status'     => 'publish',
			'date_query'      => $date_query,
			'suppress_filters' => false,
		);

		$posts = get_posts( $args );

		return array(
			'count'    => count( $posts ),
			'post_ids' => \wp_list_pluck( $posts, 'ID' ),
		);
	}
}
