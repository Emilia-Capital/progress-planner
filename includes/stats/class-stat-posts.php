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
	 * Static var to hold the stats and avoid multiple queries.
	 *
	 * @var array
	 */
	private static $stats = [];

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

		if ( ! isset( self::$stats[ $this->post_type ] ) ) {
			self::$stats[ $this->post_type ] = [];
		}
		if ( isset( self::$stats[ $this->post_type ][ $period ] ) ) {
			return self::$stats[ $this->post_type ][ $period ];
		}

		switch ( $period ) {
			case 'all':
				$stats = (array) \wp_count_posts( $this->post_type );

				self::$stats[ $this->post_type ][ $period ] = $stats;
				return $stats;

			case 'day':
				$stats = $this->get_posts_stats_by_date( 'today' );

				self::$stats[ $this->post_type ][ $period ] = $stats;
				return $stats;

			case 'week':
				$stats = $this->get_posts_stats_by_date( '-1 week' );

				self::$stats[ $this->post_type ][ $period ] = $stats;
				return $stats;

			case 'month':
				$stats = $this->get_posts_stats_by_date( '-1 month' );

				self::$stats[ $this->post_type ][ $period ] = $stats;
				return $stats;

			case 'year':
				$stats = $this->get_posts_stats_by_date( '-1 year' );

				self::$stats[ $this->post_type ][ $period ] = $stats;
				return $stats;

			default:
				$stats = $this->get_posts_stats_by_date( $period );

				self::$stats[ $this->post_type ][ $period ] = $stats;
				return $stats;
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
			'date_query'      => [
				[
					'after'     => $date_query,
					'inclusive' => true,
				],
			],
			'suppress_filters' => false,
		);

		$posts = get_posts( $args );

		// Get the number of words.
		$word_count = 0;
		foreach ( $posts as $post ) {
			$word_count += str_word_count( $post->post_content );
		}

		return array(
			'count'      => count( $posts ),
			'post_ids'   => \wp_list_pluck( $posts, 'ID' ),
			'word_count' => $word_count,
		);
	}
}
