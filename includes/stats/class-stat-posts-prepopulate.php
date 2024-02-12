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
	const POSTS_PER_PAGE = 10;

	/**
	 * Key used to store the last page that was prepopulated from the API.
	 *
	 * @var string
	 */
	const LAST_PAGE_KEY = 'last_posts_prepopulated_page';

	/**
	 * Key used to determine if prepopulating is complete.
	 *
	 * @var string
	 */
	const FINISHED_KEY = 'posts_prepopulated_complete';

	/**
	 * The last page that was prepopulated from the API.
	 *
	 * @var int
	 */
	private $last_page = 0;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Set the $last_page property.
		$this->last_page = $this->get_last_prepopulated_page();
	}

	/**
	 * Get the last page that was prepopulated from the API.
	 *
	 * @return int
	 */
	public function get_last_prepopulated_page() {
		$option_value = $this->get_value();

		return ( isset( $option_value[ self::LAST_PAGE_KEY ] ) )
			? $option_value[ self::LAST_PAGE_KEY ]
			: 0;
	}

	/**
	 * Get the total number of pages that need to be prepopulated.
	 *
	 * @return int
	 */
	public function get_total_pages() {

		$post_types = array_keys( \get_post_types( [ 'public' => true ] ) );
		$total      = 0;

		foreach ( $post_types as $post_type ) {
			$total += (int) \wp_count_posts( $post_type )->publish;
		}

		// Calculate the total number of pages.
		return (int) ceil( $total / self::POSTS_PER_PAGE );
	}

	/**
	 * Set the last page that was prepopulated from the API.
	 *
	 * @param int $page The page number.
	 *
	 * @return void
	 */
	private function set_last_prepopulated_page( $page ) {
		$option_value = $this->get_value();

		$option_value[ self::LAST_PAGE_KEY ] = $page;
		$this->set_value( [], $option_value );
	}

	/**
	 * Whether prepopulating is complete.
	 *
	 * @return bool
	 */
	public function is_prepopulating_complete() {
		$option_value = $this->get_value();
		if (
			isset( $option_value[ self::FINISHED_KEY ] ) &&
			$option_value[ self::FINISHED_KEY ]
		) {
			// Remove the last page key. It's no longer needed.
			if ( isset( $option_value[ self::LAST_PAGE_KEY ] ) ) {
				unset( $option_value[ self::LAST_PAGE_KEY ] );
				$this->set_value( [], $option_value );
			}
			return true;
		}
		return false;
	}

	/**
	 * Get posts and prepopulate the stats.
	 *
	 * @return void
	 */
	public function prepopulate() {
		// Bail early if prepopulating is complete.
		if ( $this->is_prepopulating_complete() ) {
			return;
		}
		$posts = \get_posts(
			[
				'posts_per_page'   => self::POSTS_PER_PAGE,
				'paged'            => $this->last_page + 1,
				'post_type'        => array_keys( \get_post_types( [ 'public' => true ] ) ),
				'post_status'      => 'publish',
				'suppress_filters' => false,
				// Start from oldest to newest.
				'order'            => 'ASC',
				'orderby'          => 'date',
			]
		);

		// If there are no posts for this page, then prepopulating is complete.
		if ( empty( $posts ) ) {
			$option_value = $this->get_value();

			$option_value[ self::FINISHED_KEY ] = true;
			$this->set_value( [], $option_value );
			return;
		}

		// Save the posts stats.
		foreach ( $posts as $post ) {
			$this->save_post( $post );
		}

		// Set the last page that was prepopulated from the API.
		$this->set_last_prepopulated_page( $this->last_page + 1 );
	}
}
