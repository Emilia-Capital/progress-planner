<?php
/**
 * Handle Suggestred-tasks items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks;

use Progress_Planner\Suggested_Tasks\Local_Tasks;
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
	 * @return bool
	 */
	public function evaluate_task( $task_id ) {
		$data = self::get_data_from_task_id( $task_id );
		if ( ! isset( $data['type'] ) || ! isset( $data['post_id'] ) ) {
			return false;
		}

		switch ( $data['type'] ) {
			case 'update-post':
				return $this->evaluate_update_post_task( $data );

			case 'create-post':
				return $this->evaluate_create_post_task( $data );
		}

		return false;
	}

	/**
	 * Evaluate an update-post task.
	 *
	 * @param array $data    The task data.
	 *
	 * @return bool
	 */
	public function evaluate_update_post_task( $data ) {
		global $progress_planner;
		if ( (int) \get_post_modified_time( 'U', false, (int) $data['post_id'] ) > strtotime( '-6 months' ) ) {
			$data['date'] = \gmdate( 'YW' );
			$progress_planner->get_suggested_tasks()->mark_task_as_completed( $this->get_task_id( $data ) );
			return true;
		}
		return false;
	}

	/**
	 * Evaluate a create-post task.
	 *
	 * @param array $data    The task data.
	 *
	 * @return bool
	 */
	public function evaluate_create_post_task( $data ) {
		global $progress_planner;
		$last_posts = \get_posts(
			[
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'ASC',
			]
		);
		$last_post  = $last_posts ? $last_posts[0] : null;
		if ( ! $last_post ) {
			return false;
		}

		// Check if the post was created this week.
		if ( \gmdate( 'YW', strtotime( $last_post->post_date ) ) !== \gmdate( 'YW' ) ) {
			return false;
		}

		// Check if the task is for this week.
		if ( ! isset( $data['date'] ) || $data['date'] !== \gmdate( 'YW' ) ) {
			return false;
		}

		$progress_planner->get_suggested_tasks()->mark_task_as_completed(
			self::get_task_id(
				[
					'type'    => 'create-post',
					'date'    => \gmdate( 'YW' ),
					'post_id' => $last_post->ID,
					'long'    => $progress_planner->get_helpers()->content->is_post_long( $last_post->ID ) ? '1' : '0',
				]
			)
		);
		return true;
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
		global $progress_planner;
		// Get the post that was created last.
		$last_created_posts = \get_posts(
			[
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'ASC',
			]
		);

		$is_last_post_long = (
			is_array( $last_created_posts )
			&& $progress_planner->get_helpers()->content->is_post_long( $last_created_posts[0]->ID )
		);
		$items             = [];

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
				? sprintf(
					/* translators: %d: The threshold (number, words count) for a long post. */
					esc_html__( 'Create a new short post (no longer than %d words).', 'progress-planner' ),
					Content_Helpers::LONG_POST_THRESHOLD
				)
				: sprintf(
					/* translators: %d: The threshold (number, words count) for a long post. */
					esc_html__( 'Create a new long post (longer than %d words).', 'progress-planner' ),
					Content_Helpers::LONG_POST_THRESHOLD
				),
		];
		$this->add_pending_task( $task_id );

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
			$task_id = $this->get_task_id(
				[
					'type'    => 'update-post',
					'post_id' => $post->ID,
				]
			);
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
			$this->add_pending_task( $task_id );
		}
		return $items;
	}

	/**
	 * Get the task ID.
	 *
	 * @param array $data The data to use for the task ID.
	 *
	 * @return string The task ID.
	 */
	public function get_task_id( $data ) {
		\ksort( $data );
		$parts = [];
		foreach ( $data as $key => $value ) {
			$parts[] = $key . '/' . $value;
		}
		return \implode( '|', $parts );
	}

	/**
	 * Get the data from a task-ID.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array The data.
	 */
	public static function get_data_from_task_id( $task_id ) {
		$parts = \explode( '|', $task_id );
		$data  = [];
		foreach ( $parts as $part ) {
			$part = \explode( '/', $part );
			if ( 2 !== \count( $part ) ) {
				continue;
			}
			$data[ $part[0] ] = ( \is_numeric( $part[1] ) )
				? (int) $part[1]
				: $part[1];
		}
		\ksort( $data );

		// Convert (int) 1 and (int) 0 to (bool) true and (bool) false.
		if ( isset( $data['long'] ) ) {
			$data['long'] = (bool) $data['long'];
		}

		return $data;
	}
}
