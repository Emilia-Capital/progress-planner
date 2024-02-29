<?php
/**
 * Handler for posts activities.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

use ProgressPlanner\Activities\Activity;
use ProgressPlanner\Date;

/**
 * Handler for posts activities.
 */
class Activity_Post extends Activity {

	/**
	 * Category of the activity.
	 *
	 * @var string
	 */
	protected $category = 'content';

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
		$post_types = static::get_post_types_names();
		if ( ! \in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		// Bail if the post is not published.
		if ( 'publish' !== $post->post_status ) {
			return;
		}

		// Add a publish activity.
		$activity = self::get_activity_from_post( $post );
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
			$activity = self::get_activity_from_post( $post );
			return $activity->save();
		}

		// Add an update activity.
		$activity = self::get_activity_from_post( $post );
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
		$activity = new Activity();
		$activity->set_category( 'content' );
		$activity->set_type( 'update' );
		$activity->set_date( Date::get_datetime_from_mysql_date( $post_array['post_modified'] ) );
		$activity->set_data_id( $post_id );
		$activity->set_data(
			[
				'post_type'  => $post_array['post_type'],
				'word_count' => static::get_word_count( $post_array['post_content'] ),
			]
		);
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
		$post_types = static::get_post_types_names();
		if ( ! \in_array( $post->post_type, $post_types, true ) ) {
			return;
		}

		// Add an update activity.
		$activity = self::get_activity_from_post( $post );
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
		$post_types = static::get_post_types_names();
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

		$activity = self::get_activity_from_post( $post );
		$activity->save();
	}

	/**
	 * Get an array of post-types names for the stats.
	 *
	 * @return string[]
	 */
	public static function get_post_types_names() {
		$post_types = \get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );

		return array_keys( $post_types );
	}

	/**
	 * Get words count from content.
	 *
	 * This method will render shortcodes, blocks,
	 * and strip HTML before counting the words.
	 *
	 * @param string $content The content.
	 *
	 * @return int
	 */
	public static function get_word_count( $content ) {
		// Parse blocks and shortcodes.
		$content = \do_blocks( \do_shortcode( $content ) );

		// Strip HTML.
		$content = \wp_strip_all_tags( $content, true );

		// Count words.
		return \str_word_count( $content );
	}

	/**
	 * Get Activity from WP_Post object.
	 *
	 * @param \WP_Post $post The post object.
	 *
	 * @return \ProgressPlanner\Activities\Activity
	 */
	public static function get_activity_from_post( $post ) {
		$type = 'publish' === $post->post_status ? 'publish' : 'update';
		$date = 'publish' === $post->post_status ? $post->post_date : $post->post_modified;

		$activity = new Activity();
		$activity->set_category( 'content' );
		$activity->set_type( $type );
		$activity->set_date( Date::get_datetime_from_mysql_date( $date ) );
		$activity->set_data_id( $post->ID );
		$activity->set_data(
			[
				'post_type'  => $post->post_type,
				'word_count' => static::get_word_count( $post->post_content ),
			]
		);
		return $activity;
	}
}
