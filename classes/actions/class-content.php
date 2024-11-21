<?php
/**
 * Content actions.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Actions;

/**
 * Content actions.
 */
class Content {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @return void
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
	}

	/**
	 * Post updated.
	 *
	 * Runs on post_updated hook.
	 *
	 * @param int      $post_id The post ID.
	 * @param \WP_Post $post    The post object.
	 *
	 * @return void
	 */
	public function post_updated( $post_id, $post ) {
		// Bail if we should skip saving.
		if ( $this->should_skip_saving( $post ) ) {
			return;
		}

		// Reset the words count.
		\progress_planner()->get_settings()->set( [ 'word_count', $post_id ], false );

		if ( 'publish' !== $post->post_status ) {
			return;
		}

		// Check if there is an update activity for this post, on this date.
		$existing = \progress_planner()->get_query()->query_activities(
			[
				'category'   => 'content',
				'type'       => 'update',
				'data_id'    => (string) $post_id,
				'start_date' => \progress_planner()->get_date()->get_datetime_from_mysql_date( $post->post_modified )->modify( '-12 hours' ),
				'end_date'   => \progress_planner()->get_date()->get_datetime_from_mysql_date( $post->post_modified )->modify( '+12 hours' ),
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
	 * @param int      $post_id The post ID.
	 * @param \WP_Post $post    The post object.
	 * @return void
	 */
	public function insert_post( $post_id, $post ) {
		// Bail if we should skip saving.
		if ( $this->should_skip_saving( $post ) ) {
			return;
		}

		if ( 'publish' !== $post->post_status ) {
			return;
		}

		// Check if there is a publish activity for this post.
		$existing = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				'type'     => 'publish',
				'data_id'  => (string) $post_id,
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
	 *
	 * @return void
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

		// Reset the words count.
		\progress_planner()->get_settings()->set( [ 'word_count', $post_id ], false );
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

		// Reset the words count.
		\progress_planner()->get_settings()->set( [ 'word_count', $post_id ], false );

		// Add activity.
		$activity           = new \Progress_Planner\Activities\Content();
		$activity->category = 'content';
		$activity->type     = 'delete';
		$activity->data_id  = (string) $post_id;
		$activity->date     = new \DateTime();
		$activity->user_id  = \get_current_user_id();
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
			\progress_planner()->get_activities__content_helpers()->get_post_types_names(),
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

		// Bail if an auto-draft.
		if ( 'auto-draft' === $post->post_status ) {
			return true;
		}

		return false;
	}

	/**
	 * Add an update activity.
	 *
	 * @param \WP_Post $post The post object.
	 * @param string   $type The type of activity (ie publish, update, trash, delete etc).
	 *
	 * @return void
	 */
	private function add_post_activity( $post, $type ) {

		// Post was updated to publish for the first time, ie draft was published.
		if ( 'update' === $type && 'publish' === $post->post_status ) {
			// Check if there is a publish activity for this post.
			$existing = \progress_planner()->get_query()->query_activities(
				[
					'category' => 'content',
					'type'     => 'publish',
					'data_id'  => (string) $post->ID,
				],
				'RAW'
			);

			// If there is no publish activity for this post, add it.
			if ( empty( $existing ) ) {
				$this->add_post_activity( $post, 'publish' );
				return;
			}
		}

		// Post was updated, but it was published previously.
		if ( 'update' === $type ) {

			// Check if there are any activities for this post, on this date.
			$existing = \progress_planner()->get_query()->query_activities(
				[
					'category'   => 'content',
					'data_id'    => (string) $post->ID,
					'start_date' => \progress_planner()->get_date()->get_datetime_from_mysql_date( $post->post_modified )->modify( '-12 hours' ),
					'end_date'   => \progress_planner()->get_date()->get_datetime_from_mysql_date( $post->post_modified )->modify( '+12 hours' ),
				],
				'RAW'
			);

			// If there are activities for this post, on this date, bail.
			if ( ! empty( $existing ) ) {
				// Reset the words count.
				\progress_planner()->get_settings()->set( [ 'word_count', $post->ID ], false );

				return;
			}
		}

		$activity       = \progress_planner()->get_activities__content_helpers()->get_activity_from_post( $post );
		$activity->type = $type;

		// Update the badges.
		if ( 'publish' === $type ) {

			// Check if there is a publish activity for this post.
			$existing = \progress_planner()->get_query()->query_activities(
				[
					'category' => 'content',
					'type'     => 'publish',
					'data_id'  => (string) $post->ID,
				],
				'RAW'
			);

			// If there is no publish activity for this post, add it.
			if ( empty( $existing ) ) {
				$activity->save();

				\do_action( 'progress_planner_activity_content_publish_saved', $activity->data_id );
				return;
			}
		}

		$activity->save();

		// Reset the words count.
		\progress_planner()->get_settings()->set( [ 'word_count', $post->ID ], false );
	}
}
