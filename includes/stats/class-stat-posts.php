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
	 *
	 * @return Stat_Posts Returns this object to allow chaining methods.
	 */
	public function set_post_type( $post_type ) {
		$this->post_type = $post_type;
		return $this;
	}

	/**
	 * Get the stat data.
	 *
	 * @param string $period The period to get the data for.
	 *
	 * @return array
	 */
	public function get_data( $period = 'week' ) {

		switch ( $period ) {
			case 'all':
				return (array) \wp_count_posts( $this->post_type );

			case 'day':
				return $this->get_posts_stats_by_date( [
					[
						'after'     => 'today',
						'inclusive' => true,
					],
				] );

			case 'week':
				return $this->get_posts_stats_by_date( [
					[
						'after'     => '-1 week',
						'inclusive' => true,
					],
				] );

			case 'month':
				return $this->get_posts_stats_by_date( [
					[
						'after'     => '-1 month',
						'inclusive' => true,
					],
				] );

			case 'year':
				return $this->get_posts_stats_by_date( [
					[
						'after'     => '-1 year',
						'inclusive' => true,
					],
				] );

			default:
				return $this->get_posts_stats_by_date( [
					[
						'after'     => $period,
						'inclusive' => true,
					],
				] );
		}
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
