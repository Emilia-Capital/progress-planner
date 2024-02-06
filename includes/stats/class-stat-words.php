<?php
/**
 * Stats about words.
 */

namespace ProgressPlanner\Stats;

/**
 * Stats about words.
 */
class Stat_Words extends Stat {

	/**
	 * Get the stat data.
	 *
	 * @return array
	 */
	public function get_data( $period = 'week' ) {
		// An array of post types to include in the word count.
		$post_types = [ 'post', 'page' ];

		$stats = [];

		foreach ( $post_types as $post_type ) {
			$stats[ $post_type ] = $this->get_word_count( $post_type, 'week' );
		}
		return $stats;
	}

	/**
	 * Get the word count for a post type.
	 *
	 * @param string $post_type The post type.
	 * @param string $period    The period to get the word count for.
	 * @return int
	 */
	protected function get_word_count( $post_type, $period ) {
		$posts_stats = new Stat_Posts();
		$posts_stats->set_post_type( $post_type );
		$posts_stats_data = $posts_stats->get_data( $period );

		$word_count = 0;
		if ( empty( $posts_stats_data['post_ids'] ) ) {
			return 0;
		}
		foreach ( $posts_stats_data['post_ids'] as $post_id ) {
			$post        = \get_post( $post_id );
			$word_count += str_word_count( $post->post_content );
		}
		return $word_count;
	}
}
