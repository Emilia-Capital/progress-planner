<?php
/**
 * Progress Planner main plugin class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Query;
use ProgressPlanner\Activities\Content;
use ProgressPlanner\Admin;

/**
 * Main plugin class.
 */
class Base {

	/**
	 * An instance of this class.
	 *
	 * @var \ProgressPlanner\Base
	 */
	private static $instance;

	/**
	 * The Admin object.
	 *
	 * @var \ProgressPlanner\Admin
	 */
	private $admin;

	/**
	 * Get the single instance of this class.
	 *
	 * @return \ProgressPlanner\Base
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->admin = new Admin();

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
	}

	/**
	 * Get the admin object.
	 *
	 * @return \ProgressPlanner\Admin
	 */
	public function get_admin() {
		return $this->admin;
	}

	/**
	 * Get the query object.
	 *
	 * @return \ProgressPlanner\Query
	 */
	public function get_query() {
		return Query::get_instance();
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
}
