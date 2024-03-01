<?php
/**
 * Scan existing posts and populate the options.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Scan;

use ProgressPlanner\Activities\Content_Helpers;

/**
 * Scan existing posts and populate the options.
 */
class Content {

	/**
	 * The number of posts to scan at a time.
	 *
	 * @var int
	 */
	const SCAN_POSTS_PER_PAGE = 30;

	/**
	 * The option used to store the last scanned page.
	 *
	 * @var string
	 */
	const LAST_SCANNED_PAGE_OPTION = 'progress_planner_stats_last_scanned_page';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		\add_action( 'save_post', [ $this, 'save_post' ], 10, 2 );
		\add_action( 'wp_insert_post', [ $this, 'insert_post' ], 10, 2 );
		\add_action( 'transition_post_status', [ $this, 'transition_post_status' ], 10, 3 );
		\add_action( 'wp_trash_post', [ $this, 'trash_post' ] );
		\add_action( 'delete_post', [ $this, 'delete_post' ] );
		\add_action( 'pre_post_update', [ $this, 'pre_post_update' ], 10, 2 );

		\add_action( 'wp_ajax_progress_planner_scan_posts', [ $this, 'ajax_scan' ] );
		\add_action( 'wp_ajax_progress_planner_reset_stats', [ $this, 'ajax_reset_stats' ] );
	}


	/**
	 * Save post stats.
	 *
	 * Runs on save_post hook.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 */
	public function save_post( $post_id, $post ) {
		$this->insert_post( $post_id, $post );
	}

	/**
	 * Insert a post.
	 *
	 * Runs on wp_insert_post hook.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 * @return void
	 */
	public function insert_post( $post_id, $post ) {
		// Bail if the post is not included in the post-types we're tracking.
		$post_types = Content_Helpers::get_post_types_names();
		if ( ! \in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		// Add a publish activity.
		$activity = Content_Helpers::get_activity_from_post( $post );
		$activity->save();
	}

	/**
	 * Run actions when transitioning a post status.
	 *
	 * @param string   $new_status The new status.
	 * @param string   $old_status The old status.
	 * @param \WP_Post $post       The post object.
	 */
	public function transition_post_status( $new_status, $old_status, $post ) {

		// If the post is published, check if it was previously published,
		// and if so, delete the old activity and create a new one.
		if ( 'publish' !== $old_status && 'publish' === $new_status ) {
			$old_publish_activities = \progress_planner()->get_query()->query_activities(
				[
					'category' => 'content',
					'type'     => 'publish',
					'data_id'  => $post->ID,
				]
			);
			if ( ! empty( $old_publish_activities ) ) {
				foreach ( $old_publish_activities as $activity ) {
					$activity->delete();
				}
			}

			// Add a publish activity.
			$activity = Content_Helpers::get_activity_from_post( $post );
			return $activity->save();
		}

		// Add an update activity.
		$activity = Content_Helpers::get_activity_from_post( $post );
		return $activity->save();
	}

	/**
	 * Update a post.
	 *
	 * Runs on pre_post_update hook.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 *
	 * @return bool
	 */
	public function pre_post_update( $post_id, $post ) {
		$post_array = (array) $post;
		// Add an update activity.
		$activity = new Content();
		$activity->set_category( 'content' );
		$activity->set_type( 'update' );
		$activity->set_date( Date::get_datetime_from_mysql_date( $post_array['post_modified'] ) );
		$activity->set_data_id( $post_id );
		$activity->set_user_id( (int) $post_array['post_author'] );
		return $activity->save();
	}

	/**
	 * Trash a post.
	 *
	 * Runs on wp_trash_post hook.
	 *
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public function trash_post( $post_id ) {
		$post = \get_post( $post_id );

		// Bail if the post is not included in the post-types we're tracking.
		$post_types = Content_Helpers::get_post_types_names();
		if ( ! \in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		// Add an update activity.
		$activity = Content_Helpers::get_activity_from_post( $post );
		$activity->set_type( 'update' );
		$activity->set_date( Date::get_datetime_from_mysql_date( $post->post_modified ) );
		return $activity->save();
	}

	/**
	 * Delete a post.
	 *
	 * Runs on delete_post hook.
	 *
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public function delete_post( $post_id ) {
		$post = \get_post( $post_id );

		// Bail if the post is not included in the post-types we're tracking.
		$post_types = Content_Helpers::get_post_types_names();
		if ( ! \in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		// Update existing activities, and remove the words count.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				'data_id'  => $post->ID,
			]
		);
		if ( ! empty( $activities ) ) {
			\progress_planner()->get_query()->delete_activities( $activities );
		}

		$activity = Content_Helpers::get_activity_from_post( $post );
		$activity->save();
	}

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
		foreach ( Content_Helpers::get_post_types_names() as $post_type ) {
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
				'post_type'      => Content_Helpers::get_post_types_names(),
				'post_status'    => 'publish',
			]
		);

		if ( ! $posts ) {
			\delete_option( static::LAST_SCANNED_PAGE_OPTION );
			return [
				'lastScannedPage' => $current_page,
				'lastPage'        => $total_pages,
				'progress'        => 100,
			];
		}

		// Loop through the posts and update the stats.
		$activities = [];
		foreach ( $posts as $post ) {
			$activities[ $post->ID ] = Content_Helpers::get_activity_from_post( $post );
		}
		\progress_planner()->get_query()->insert_activities( $activities );
		\update_option( static::LAST_SCANNED_PAGE_OPTION, $current_page );

		return [
			'lastScannedPage' => $current_page,
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
		\progress_planner()->get_query()->delete_category_activities( 'content' );
		\delete_option( static::LAST_SCANNED_PAGE_OPTION );
	}

	/**
	 * Ajax scan.
	 *
	 * @return void
	 */
	public function ajax_scan() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner_scan', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		// Scan the posts.
		$updated_stats = static::update_stats();

		\wp_send_json_success(
			[
				'lastScanned' => $updated_stats['lastScannedPage'],
				'lastPage'    => $updated_stats['lastPage'],
				'progress'    => $updated_stats['progress'],
				'messages'    => [
					'scanComplete' => \esc_html__( 'Scan complete.', 'progress-planner' ),
				],
			]
		);
	}

	/**
	 * Ajax reset stats.
	 *
	 * @return void
	 */
	public function ajax_reset_stats() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner_scan', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		// Reset the stats.
		static::reset_stats();

		\wp_send_json_success(
			[
				'message' => \esc_html__( 'Stats reset. Refreshing the page...', 'progress-planner' ),
			]
		);
	}
}
