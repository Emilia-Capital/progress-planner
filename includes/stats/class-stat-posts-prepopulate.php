<?php
/**
 * Prepopulate the posts stats.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Stats;

/**
 * Prepopulate the posts stats.
 */
class Stat_Posts_Prepopulate extends Stat_Posts {

	/**
	 * The number of posts to prepopulate at a time.
	 *
	 * @var int
	 */
	const POSTS_PER_PAGE = 100;

	/**
	 * The last post-ID.
	 *
	 * @var int
	 */
	private $last_post_id = 0;

	/**
	 * The last scanned post-ID.
	 *
	 * @var int
	 */
	private $last_scanned_post_id = 0;

	/**
	 * Get the last page that was prepopulated from the API.
	 *
	 * @return int
	 */
	public function get_last_prepopulated_post() {
		// If we have the last scanned post, return it.
		if ( $this->last_scanned_post_id ) {
			return $this->last_scanned_post_id;
		}

		// Try to get the value from the transient.
		$cached = \get_transient( 'progress_planner_last_prepopulated_post' );
		if ( $cached ) {
			$this->last_scanned_post_id = $cached;
			return $this->last_scanned_post_id;
		}

		// Get the last scanned post-ID from the stats.
		$option_value = $this->get_value();
		foreach ( $option_value as $posts ) {
			foreach ( $posts as $post_id => $details ) {
				if ( $post_id > $this->last_scanned_post_id ) {
					$this->last_scanned_post_id = $post_id;
				}
			}
		}
		return $this->last_scanned_post_id;
	}

	/**
	 * Set the last prepopulated post.
	 */
	public function save_last_prepopulated_post() {
		\set_transient( 'progress_planner_last_prepopulated_post', $this->last_scanned_post_id, \HOUR_IN_SECONDS );
	}

	/**
	 * Get posts and prepopulate the stats.
	 *
	 * @return void
	 */
	public function prepopulate() {
		// Get the last post we processed.
		$last_id = $this->get_last_prepopulated_post();

		// Build an array of posts to save.
		$post_ids = \range( $last_id, $last_id + self::POSTS_PER_PAGE );

		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );

			// If the post doesn't exist or is not publish, skip it.
			if ( ! $post || 'publish' !== $post->post_status ) {
				$this->last_scanned_post_id = $post_id;
				$this->save_last_prepopulated_post();
				continue;
			}

			$this->save_post( $post );
			$this->last_scanned_post_id = $post->ID;
			$this->save_last_prepopulated_post();
		}
	}

	/**
	 * Get the last post-ID created.
	 *
	 * @return int
	 */
	public function get_last_post_id() {
		if ( $this->last_post_id ) {
			return $this->last_post_id;
		}
		$last_post = \get_posts(
			[
				'posts_per_page'   => 1,
				'post_type'        => $this->get_post_types_names(),
				'post_status'      => 'publish',
				'suppress_filters' => false,
				'order'            => 'DESC',
				'orderby'          => 'ID',
			]
		);
		if ( empty( $last_post ) ) {
			return 0;
		}
		$this->last_post_id = $last_post[0]->ID;
		return $this->last_post_id;
	}
}
