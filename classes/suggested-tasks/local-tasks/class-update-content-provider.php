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
class Update_Content_Provider extends Content_Provider_Abstract implements \Progress_Planner\Suggested_Tasks\Local_Tasks_Provider_Interface {

	/**
	 * The provider ID.
	 *
	 * @var string
	 */
	const TYPE = 'update-post';

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
	 * Get the provider ID.
	 *
	 * @return string
	 */
	public function get_provider_type() {
		return self::TYPE;
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

		if ( isset( $data['post_id'] ) && (int) \get_post_modified_time( 'U', false, (int) $data['post_id'] ) > strtotime( '-6 months' ) ) {
			$data['date'] = \gmdate( 'YW' );
			return $this->get_task_id( $data );
		}
		return false;
	}

	/**
	 * Get an array of tasks to inject.
	 *
	 * @return array
	 */
	public function get_tasks_to_inject() {
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
			if ( strtotime( $post->post_modified ) > strtotime( '-6 months' ) ) {
				continue;
			}
			$task_id = $this->get_task_id(
				[
					'type'    => 'update-post',
					'post_id' => $post->ID,
				]
			);
			$items[] = $this->get_task_details( $task_id );

		}
		return $items;
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

		$post         = \get_post( $data['post_id'] );
		$task_details = [
			'task_id'     => $task_id,
			'title'       => sprintf( 'Update post "%s"', \esc_html( $post->post_title ) ),
			'parent'      => 0,
			'priority'    => 'high',
			'type'        => 'writing',
			'points'      => 1,
			'url'         => \esc_url( \get_edit_post_link( $post->ID ) ),
			'description' => '<p>' . sprintf(
				/* translators: %s: The post title. */
				\esc_html__( 'Update the post "%s" as it was last updated more than 6 months ago.', 'progress-planner' ),
				\esc_html( $post->post_title )
			) . '</p><p><a href="' . \esc_url( \get_edit_post_link( $post->ID ) ) . '">' . \esc_html__( 'Edit the post', 'progress-planner' ) . '</a>.</p>',
		];

		return $task_details;
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
