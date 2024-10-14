<?php
/**
 * Handle Suggestred-tasks items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks;

use Progress_Planner\Suggested_Tasks\Local_Tasks;
use Progress_Planner\Suggested_Tasks;
use Progress_Planner\Activities\Content_Helpers;

/**
 * Handle Suggestred-tasks items.
 */
class Update_Posts extends Local_Tasks {

	/**
	 * The number of items to inject.
	 *
	 * @var int
	 */
	const ITEMS_TO_INJECT = 2;

	/**
	 * Evaluate a task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return void
	 */
	public function evaluate_task( $task_id ) {
		if ( \str_starts_with( 'update-post-', $task_id ) ) {
			$post_id = (int) \str_replace( 'update-post-', '', $task_id );
			$post    = \get_post( $post_id );
			if ( strtotime( $post->post_modified ) > strtotime( '-6 months' ) ) {
				Suggested_Tasks::mark_task_as_completed( $task_id . '-' . \gmdate( 'Y-m-d' ) );
				self::remove_pending_task( $task_id );
			}
		}
	}

	/**
	 * Get an array of tasks to inject.
	 *
	 * @return array
	 */
	public function get_tasks_to_inject() {
		return array_merge(
			$this->get_tasks_to_update_posts(),
			$this->get_tasks_to_create_posts()
		);
	}

	/**
	 * Get tasks to create posts.
	 *
	 * @return array
	 */
	public function get_tasks_to_create_posts() {
		// Get the post that was created last.
		$last_created_posts = \get_posts(
			[
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'ASC',
			]
		);
		// Get the word count of the last created post.
		$word_count = 0;
		if ( $last_created_posts ) {
			$word_count = Content_Helpers::get_word_count(
				$last_created_posts[0]->post_content,
				$last_created_posts[0]->ID
			);
		}

		$long_post_threshold = 300;
		$is_last_post_long   = $word_count > $long_post_threshold;
		$items               = [];

		$task_id  = 'create-post-';
		$task_id .= $is_last_post_long ? 'short' : 'long';
		// Append the date formatted as Year - Week.
		$task_id .= '-' . \gmdate( 'YW' );

		$items[] = [
			'task_id'     => $task_id,
			'title'       => $is_last_post_long
				? esc_html__( 'Create a short post', 'progress-planner' )
				: esc_html__( 'Create a long post', 'progress-planner' ),
			'parent'      => 0,
			'priority'    => 'medium',
			'type'        => 'writing',
			'description' => $is_last_post_long
				? esc_html__( 'Create a new short post.', 'progress-planner' )
				: esc_html__( 'Create a new long post.', 'progress-planner' ),
		];
		self::add_pending_task( $task_id );

		return $items;
	}

	/**
	 * Get tasks to update posts.
	 *
	 * @return array
	 */
	public function get_tasks_to_update_posts() {
		// Get the post that was updated last.
		$last_updated_posts = \get_posts(
			[
				'posts_per_page' => self::ITEMS_TO_INJECT,
				'post_status'    => 'publish',
				'orderby'        => 'modified',
				'order'          => 'ASC',
			]
		);

		if ( ! $last_updated_posts ) {
			return [];
		}

		$items = [];
		foreach ( $last_updated_posts as $post ) {
			// If the last update was more than 6 months ago, add a task.
			if ( strtotime( $post->post_modified ) > strtotime( '-6 months' ) ) {
				continue;
			}
			$task_id = "update-post-{$post->ID}";
			$items[] = [
				'task_id'     => $task_id,
				'title'       => sprintf( 'Update post "%s"', \esc_html( $post->post_title ) ),
				'parent'      => 0,
				'priority'    => 'high',
				'type'        => 'writing',
				'description' => '<p>' . sprintf(
					/* translators: %s: The post title. */
					\esc_html__( 'Update the post "%s" as it was last updated more than 6 months ago.', 'progress-planner' ),
					\esc_html( $post->post_title )
				) . '</p><p><a href="' . \esc_url( \get_edit_post_link( $post->ID ) ) . '">' . \esc_html__( 'Edit the post', 'progress-planner' ) . '</a>.</p>',
			];
			self::add_pending_task( $task_id );
		}
		return $items;
	}
}
