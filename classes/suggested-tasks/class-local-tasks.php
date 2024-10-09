<?php
/**
 * Handle TODO list items.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks;

/**
 * Settings class.
 */
class Local_Tasks {

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_filter( 'progress_planner_suggested_tasks_api_items', [ $this, 'inject_task_update_old_post' ] );
		\add_filter( 'progress_planner_suggested_tasks_api_items', [ $this, 'inject_last_update_core' ] );
	}

	/**
	 * Filter the tasks.
	 *
	 * @param array $tasks The tasks.
	 *
	 * @return array
	 */
	public function inject_task_update_old_post( $tasks ) {
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}
		// Get the post that was updated last.
		$last_updated_posts = \get_posts(
			[
				'posts_per_page' => 2,
				'post_status'    => 'publish',
				'orderby'        => 'modified',
				'order'          => 'ASC',
			]
		);

		if ( ! $last_updated_posts ) {
			return $tasks;
		}

		$inject_items = [];
		foreach ( $last_updated_posts as $post ) {
			// If the last update was more than 6 months ago, add a task.
			if ( strtotime( $post->post_modified ) > strtotime( '-6 months' ) ) {
				continue;
			}
			$inject_items[] = [
				'task_id'               => md5( (string) $post->ID ),
				'title'                 => sprintf( 'Update post "%s"', \esc_html( $post->post_title ) ),
				'parent'                => 0,
				'priority'              => 'high',
				'type'                  => 'writing',
				'premium'               => 'no',
				'description'           => '<p>' . sprintf(
					/* translators: %s: The post title. */
					\esc_html__( 'Update the post "%s" as it was last updated more than 6 months ago.', 'progress-planner' ),
					\esc_html( $post->post_title )
				) . '</p><p><a href="' . \esc_url( \get_edit_post_link( $post->ID ) ) . '">Edit the post</a>.</p>',
				'completion_type'       => 'auto',
				'evaluation_conditions' => false,
			];
		}
		return \array_merge( $inject_items, $tasks );
	}

	/**
	 * Filter the tasks.
	 *
	 * @param array $tasks The tasks.
	 *
	 * @return array
	 */
	public function inject_last_update_core( $tasks ) {
		if ( ! is_array( $tasks ) ) {
			$tasks = [];
		}
		// If all updates are performed, do not add the task.
		if ( 0 === \wp_get_update_data()['counts']['total'] ) {
			return $tasks;
		}

		$inject_items = [
			[
				'task_id'               => 'update-core',
				'title'                 => \esc_html__( 'Perform all updates', 'progress-planner' ),
				'parent'                => 0,
				'priority'              => 'high',
				'type'                  => 'maintenance',
				'premium'               => 'no',
				'description'           => '<p>' . \esc_html__( 'Perform all updates to ensure your website is secure and up-to-date.', 'progress-planner' ) . '</p>',
				'completion_type'       => 'auto',
				'evaluation_conditions' => false,
			],
		];
		return \array_merge( $inject_items, $tasks );
	}
}
