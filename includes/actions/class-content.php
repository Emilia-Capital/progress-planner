<?php
/**
 * Scan existing posts and populate the options.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Actions;

use ProgressPlanner\Activities\Content_Helpers;
use ProgressPlanner\Activities\Content as Content_Activity;
use ProgressPlanner\Date;
use ProgressPlanner\Settings;

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
	const LAST_SCANNED_PAGE_OPTION = 'content_last_scanned_page';

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
		// Add activity when a post is updated.
		\add_action( 'post_updated', [ $this, 'post_updated' ], 10, 2 );

		// Add activity when a post is added.
		\add_action( 'wp_insert_post', [ $this, 'insert_post' ], 10, 2 );
		\add_action( 'transition_post_status', [ $this, 'transition_post_status' ], 10, 3 );

		// Add activity when a post is trashed or deleted.
		\add_action( 'wp_trash_post', [ $this, 'trash_post' ] );
		\add_action( 'delete_post', [ $this, 'delete_post' ] );

		// Add hooks to handle scanning existing posts.
		\add_action( 'wp_ajax_progress_planner_scan_posts', [ $this, 'ajax_scan' ] );
		\add_action( 'wp_ajax_progress_planner_reset_stats', [ $this, 'ajax_reset_stats' ] );
	}

	/**
	 * Post updated.
	 *
	 * Runs on post_updated hook.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post    The post object.
	 *
	 * @return void
	 */
	public function post_updated( $post_id, $post ) {
		// Bail if we should skip saving.
		if ( $this->should_skip_saving( $post ) ) {
			return;
		}

		// Check if there is an update activity for this post, on this date.
		$existing = \progress_planner()->get_query()->query_activities(
			[
				'category'   => 'content',
				'type'       => 'update',
				'data_id'    => $post_id,
				'start_date' => Date::get_datetime_from_mysql_date( $post->post_modified )->modify( '-12 hours' ),
				'end_date'   => Date::get_datetime_from_mysql_date( $post->post_modified )->modify( '+12 hours' ),
			],
			'RAW'
		);

		// If there is an update activity for this post, on this date, bail.
		if ( ! empty( $existing ) ) {
			return;
		}

		$this->add_post_activity( $post, 'update' );
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
		// Bail if we should skip saving.
		if ( $this->should_skip_saving( $post ) ) {
			return;
		}

		// Check if there is an publish activity for this post.
		$existing = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				'type'     => 'publish',
				'data_id'  => $post_id,
			],
			'RAW'
		);

		// If there is a publish activity for this post, bail.
		if ( ! empty( $existing ) ) {
			return;
		}

		// Add a publish activity.
		$this->add_post_activity( $post, 'publish' );
	}

	/**
	 * Run actions when transitioning a post status.
	 *
	 * @param string   $new_status The new status.
	 * @param string   $old_status The old status.
	 * @param \WP_Post $post       The post object.
	 */
	public function transition_post_status( $new_status, $old_status, $post ) {
		// Bail if we should skip saving.
		if ( $this->should_skip_saving( $post ) ||
			$new_status === $old_status ||
			( 'publish' !== $new_status && 'publish' !== $old_status )
		) {
			return;
		}

		// Add activity.
		$this->add_post_activity( $post, $new_status === 'publish' ? 'publish' : 'update' );
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

		// Bail if we should skip saving.
		if ( $this->should_skip_saving( $post ) ) {
			return;
		}

		$this->add_post_activity( $post, 'trash' );
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

		// Bail if we should skip saving.
		if ( $this->should_skip_saving( $post ) ) {
			return;
		}

		// Add activity.
		$activity = new Content_Activity();
		$activity->set_category( 'content' );
		$activity->set_type( 'delete' );
		$activity->set_data_id( $post_id );
		$activity->set_date( new \DateTime() );
		$activity->set_user_id( get_current_user_id() );
		$activity->save();
	}

	/**
	 * Basic conditions to determine if we should skip saving.
	 *
	 * @param \WP_Post $post The post object.
	 *
	 * @return bool
	 */
	private function should_skip_saving( $post ) {
		// Bail if the post is not included in the post-types we're tracking.
		if ( ! \in_array(
			$post->post_type,
			Content_Helpers::get_post_types_names(),
			true
		) ) {
			return true;
		}

		// Bail if this is an auto-save.
		if ( \defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return true;
		}

		// Bail if this is a revision.
		if ( \wp_is_post_revision( $post->ID ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add an update activity.
	 *
	 * @param \WP_Post $post The post object.
	 * @param string   $type The type of activity.
	 *
	 * @return void
	 */
	private function add_post_activity( $post, $type ) {
		if ( 'update' === $type ) {
			if ( 'publish' === $post->post_status ) {
				// Check if there is a publish activity for this post.
				$existing = \progress_planner()->get_query()->query_activities(
					[
						'category' => 'content',
						'type'     => 'publish',
						'data_id'  => $post_id,
					],
					'RAW'
				);

				// If there is no publish activity for this post, add it.
				if ( empty( $existing ) ) {
					$this->add_post_activity( $post, 'publish' );
					return;
				}
			}

			// Check if there are any activities for this post, on this date.
			$existing = \progress_planner()->get_query()->query_activities(
				[
					'category'   => 'content',
					'data_id'    => $post->ID,
					'start_date' => Date::get_datetime_from_mysql_date( $post->post_modified )->modify( '-12 hours' ),
					'end_date'   => Date::get_datetime_from_mysql_date( $post->post_modified )->modify( '+12 hours' ),
				],
				'RAW'
			);

			// If there are activities for this post, on this date, bail.
			if ( ! empty( $existing ) ) {
				return;
			}
		}

		$activity = Content_Helpers::get_activity_from_post( $post );
		$activity->set_type( $type );
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
		$last_page = (int) Settings::get( static::LAST_SCANNED_PAGE_OPTION, 0 );
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
			Settings::delete( static::LAST_SCANNED_PAGE_OPTION );
			Settings::set( 'content_scanned', true );
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
		Settings::set( static::LAST_SCANNED_PAGE_OPTION, $current_page );

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
		\progress_planner()->get_query()->delete_category_activities( 'maintenance' );
		Settings::delete_all();
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
