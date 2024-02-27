<?php
/**
 * Scan existing posts and populate the options.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Scan;

use ProgressPlanner\Activities\Activity;
use ProgressPlanner\Date;
use ProgressPlanner\Activities\Activity_Post;
use ProgressPlanner\Activities\Query;

/**
 * Scan existing posts and populate the options.
 */
class Posts {

	/**
	 * The number of posts to scan at a time.
	 *
	 * @var int
	 */
	const SCAN_POSTS_PER_PAGE = 50;

	/**
	 * The option used to store the last scanned page.
	 *
	 * @var string
	 */
	const LAST_SCANNED_PAGE_OPTION = 'progress_planner_stats_last_scanned_page';

	/**
	 * Update stats for posts.
	 * - Gets the next page to scan.
	 * - Gets the posts for the page.
	 * - Updates the stats for the posts.
	 * - Updates the last scanned page option.
	 *
	 * @return array
	 */
	public static function update_stats() {

		// Get the total number of posts.
		$total_posts_count = 0;
		foreach ( Activity_Post::get_post_types_names() as $post_type ) {
			$total_posts_count += \wp_count_posts( $post_type )->publish;
		}
		// Calculate the total pages to scan.
		$total_pages = \ceil( $total_posts_count / static::SCAN_POSTS_PER_PAGE );
		// Get the last scanned page.
		$last_page = (int) \get_option( static::LAST_SCANNED_PAGE_OPTION, 0 );
		// The current page to scan.
		$current_page = $last_page + 1;

		// Get posts.
		$posts = \get_posts(
			[
				'posts_per_page' => static::SCAN_POSTS_PER_PAGE,
				'paged'          => $current_page,
				'post_type'      => Activity_Post::get_post_types_names(),
				'post_status'    => 'any',
			]
		);

		if ( ! $posts ) {
			\delete_option( static::LAST_SCANNED_PAGE_OPTION );
			return [
				'lastScannedPage' => $last_page,
				'lastPage'        => $total_pages,
				'progress'        => 100,
			];
		}

		// Loop through the posts and update the stats.
		foreach ( $posts as $post ) {
			$activity = new Activity();
			$activity->set_category( 'post' );
			$activity->set_data_id( $post->ID );
			$activity->set_data(
				[
					'post_type'  => $post->post_type,
					'word_count' => Activity_Post::get_word_count( $post->post_content ),
				]
			);

			switch ( $post->post_status ) {
				case 'publish':
					$activity->set_type( 'publish' );
					$activity->set_date( Date::get_datetime_from_mysql_date( $post->post_date ) );
					break;

				default:
					$activity->set_type( 'update' );
					$activity->set_date( Date::get_datetime_from_mysql_date( $post->post_modified ) );
			}

			$activity->save();
		}

		\update_option( static::LAST_SCANNED_PAGE_OPTION, $current_page );

		return [
			'lastScannedPage' => $last_page,
			'lastPage'        => $total_pages,
			'progress'        => round( ( $current_page / max( 1, $total_pages ) ) * 100 ),
		];
	}

	/**
	 * Reset the stats in our database.
	 *
	 * @return void
	 */
	public static function reset_stats() {
		Query::get_instance()->delete_category_activities( 'post' );
		\delete_option( static::LAST_SCANNED_PAGE_OPTION );
	}
}
