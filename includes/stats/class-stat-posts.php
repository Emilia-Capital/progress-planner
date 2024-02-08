<?php
/**
 * Stats about posts.
 *
 * @package ProgressPlanner
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
	 * Get the data.
	 *
	 * @return array
	 */
	public function get_data() {
		$args = [
			'posts_per_page'   => 1000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'post_type'        => $this->post_type,
			'post_status'      => 'publish',
			'date_query'       => $this->date_query,
			'suppress_filters' => false,
		];

		$posts = get_posts( $args );

		// Get the number of words.
		$word_count = 0;
		foreach ( $posts as $post ) {
			$word_count += str_word_count( $post->post_content );
		}

		return [
			'count'      => count( $posts ),
			'post_ids'   => \wp_list_pluck( $posts, 'ID' ),
			'word_count' => $word_count,
		];
	}
}
