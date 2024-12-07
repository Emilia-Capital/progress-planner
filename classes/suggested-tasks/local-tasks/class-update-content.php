<?php
/**
 * Add tasks for content updates.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks;

/**
 * Add tasks for content updates.
 */
class Update_Content implements \Progress_Planner\Suggested_Tasks\Local_Tasks_Interface {

	/**
	 * The number of items to inject.
	 *
	 * @var int
	 */
	const ITEMS_TO_INJECT = 2;

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'progress_planner_update_posts_tasks_args', [ $this, 'filter_update_posts_args' ] );
	}

	/**
	 * Evaluate a task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool|string
	 */
	public function evaluate_task( $task_id ) {
		$data = $this->get_data_from_task_id( $task_id );
		if ( ! isset( $data['type'] ) ) {
			return false;
		}

		switch ( $data['type'] ) {
			case 'update-post':
				return $this->evaluate_update_post_task( $task_id );

			case 'create-post':
				return $this->evaluate_create_post_task( $task_id );
		}

		return false;
	}

	/**
	 * Evaluate an update-post task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool|string
	 */
	public function evaluate_update_post_task( $task_id ) {
		$data = $this->get_data_from_task_id( $task_id );

		if ( isset( $data['post_id'] ) && (int) \get_post_modified_time( 'U', false, (int) $data['post_id'] ) > strtotime( '-6 months' ) ) {
			$data['date'] = \gmdate( 'YW' );
			return $this->get_task_id( $data );
		}
		return false;
	}

	/**
	 * Evaluate a create-post task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return bool|string
	 */
	public function evaluate_create_post_task( $task_id ) {
		$data = $this->get_data_from_task_id( $task_id );

		$last_posts = \get_posts(
			[
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			]
		);

		$last_post = $last_posts ? $last_posts[0] : null;
		if ( ! $last_post ) {
			return false;
		}

		// Check if the post was created this week.
		if ( \gmdate( 'YW', strtotime( $last_post->post_date ) ) !== \gmdate( 'YW' ) ) { // @phpstan-ignore-line argument.type
			return false;
		}

		// Check if the task is for this week.
		if ( ! isset( $data['date'] ) || (string) $data['date'] !== (string) \gmdate( 'YW' ) ) {
			return false;
		}

		// Check if the task is for a long post.
		$is_post_long = \progress_planner()->get_activities__content_helpers()->is_post_long( $last_post->ID );
		if ( ! isset( $data['long'] ) || $data['long'] !== $is_post_long ) {
			return false;
		}

		return $this->get_task_id(
			[
				'type'    => 'create-post',
				'date'    => \gmdate( 'YW' ),
				'post_id' => $last_post->ID,
				'long'    => $is_post_long ? '1' : '0',
			]
		);
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

		// Early exit if we have both long and short posts snoozed.
		if ( true === \progress_planner()->get_suggested_tasks()->check_task_condition(
			[
				'type'         => 'snoozed-post-length',
				'post_lengths' => [ 'long', 'short' ],
			]
		) ) {
			return [];
		}

		// Get the post that was created last.
		$last_created_posts = \get_posts(
			[
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			]
		);

		$is_last_post_long = (
			! empty( $last_created_posts )
			&& \progress_planner()->get_activities__content_helpers()->is_post_long( $last_created_posts[0]->ID )
		);

		// If the task with this length is snoozed, don't add a task.
		if ( true === \progress_planner()->get_suggested_tasks()->check_task_condition(
			[
				'type'         => 'snoozed-post-length',
				'post_lengths' => [ $is_last_post_long ? 'short' : 'long' ],
			]
		) ) {
			return [];
		}

		$task_id = $this->get_task_id(
			[
				'type' => 'create-post',
				'date' => \gmdate( 'YW' ),
				'long' => $is_last_post_long ? '0' : '1',
			]
		);

		// If the task with this length and id is completed, don't add a task.
		if ( true === \progress_planner()->get_suggested_tasks()->check_task_condition(
			[
				'type'    => 'completed',
				'task_id' => $task_id,
			]
		) ) {
			return [];
		}

		return [ $this->get_task_details( $task_id ) ];
	}

	/**
	 * Get the task details.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return array
	 */
	public function get_task_details( $task_id ) {

		$data = $this->get_data_from_task_id( $task_id );

		$task_details = [];

		if ( 'create-post' === $data['type'] ) {
			$task_details = [
				'task_id'     => $task_id,
				'title'       => isset( $data['long'] ) && $data['long']
					? esc_html__( 'Create a long post', 'progress-planner' )
					: esc_html__( 'Create a short post', 'progress-planner' ),
				'parent'      => 0,
				'priority'    => 'medium',
				'type'        => 'writing',
				'points'      => isset( $data['long'] ) && $data['long'] ? 2 : 1,
				'url'         => \esc_url( \admin_url( 'post-new.php?post_type=post' ) ),
				'description' => isset( $data['long'] ) && $data['long']
					? sprintf(
						/* translators: %d: The threshold (number, words count) for a long post. */
						esc_html__( 'Create a new short post (no longer than %d words).', 'progress-planner' ),
						\Progress_Planner\Activities\Content_Helpers::LONG_POST_THRESHOLD
					)
					: sprintf(
						/* translators: %d: The threshold (number, words count) for a long post. */
						esc_html__( 'Create a new long post (longer than %d words).', 'progress-planner' ),
						\Progress_Planner\Activities\Content_Helpers::LONG_POST_THRESHOLD
					),
			];
		} elseif ( 'update-post' === $data['type'] ) {
			$post         = \get_post( $data['post_id'] );
			$task_details = [
				'task_id'     => $task_id,
				'title'       => sprintf( 'Update post "%s"', \esc_html( $post->post_title ) ), // @phpstan-ignore-line property.nonObject
				'parent'      => 0,
				'priority'    => 'high',
				'type'        => 'writing',
				'points'      => 1,
				'url'         => \esc_url( \get_edit_post_link( $post->ID ) ), // @phpstan-ignore-line property.nonObject
				'description' => '<p>' . sprintf(
					/* translators: %s: The post title. */
					\esc_html__( 'Update the post "%s" as it was last updated more than 6 months ago.', 'progress-planner' ),
					\esc_html( $post->post_title ) // @phpstan-ignore-line property.nonObject
				) . '</p><p><a href="' . \esc_url( \get_edit_post_link( $post->ID ) ) . '">' . \esc_html__( 'Edit the post', 'progress-planner' ) . '</a>.</p>', // @phpstan-ignore-line property.nonObject
			];
		}

		return $task_details;
	}

	/**
	 * Get tasks to update posts.
	 *
	 * @return array
	 */
	public function get_tasks_to_update_posts() {

		$args = apply_filters(
			'progress_planner_update_posts_tasks_args',
			[
				'posts_per_page' => self::ITEMS_TO_INJECT,
				'post_status'    => 'publish',
				'orderby'        => 'modified',
				'order'          => 'ASC',
			],
		);

		// Get the post that was updated last.
		$last_updated_posts = \get_posts( $args );

		if ( ! $last_updated_posts ) {
			return [];
		}

		$items = [];
		foreach ( $last_updated_posts as $post ) {
			// If the last update was more than 6 months ago, add a task.
			if ( strtotime( $post->post_modified ) > strtotime( '-6 months' ) ) { // @phpstan-ignore-line property.nonObject
				continue;
			}
			$task_id = $this->get_task_id(
				[
					'type'    => 'update-post',
					'post_id' => $post->ID, // @phpstan-ignore-line property.nonObject
				]
			);
			$items[] = $this->get_task_details( $task_id );

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
	public function get_data_from_task_id( $task_id ) {
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

	/**
	 * Filter the update posts tasks args.
	 *
	 * @param array $args The args.
	 *
	 * @return array
	 */
	public function filter_update_posts_args( $args ) {
		$snoozed          = \progress_planner()->get_suggested_tasks()->get_snoozed_tasks();
		$snoozed_post_ids = [];

		if ( \is_array( $snoozed ) && ! empty( $snoozed ) ) {
			foreach ( $snoozed as $task ) {
				$data = $this->get_data_from_task_id( $task['id'] );
				if ( isset( $data['type'] ) && 'update-post' === $data['type'] ) {
					$snoozed_post_ids[] = $data['post_id'];
				}
			}

			if ( ! empty( $snoozed_post_ids ) ) {
				$args['post__not_in'] = $snoozed_post_ids;
			}
		}

		return $args;
	}
}
