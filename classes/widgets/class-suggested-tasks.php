<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * Published Content Widget.
 */
final class Suggested_Tasks extends Widget {


	/**
	 * The remote server URL.
	 *
	 * @var string
	 */
	const REMOTE_DOMAIN = 'https://progressplanner.com';

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'suggested-todo';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		$api_tasks = $this->get_tasks();

		$tasks = [];

		// Get high-priority tasks.
		$tasks['high'] = \array_filter(
			$api_tasks,
			function ( $task ) {
				return 'high' === $task['priority'];
			}
		);

		// Get medium-priority tasks.
		$tasks['medium'] = \array_filter(
			$api_tasks,
			function ( $task ) {
				return 'medium' === $task['priority'];
			}
		);

		// Get low-priority tasks.
		$tasks['low'] = \array_filter(
			$api_tasks,
			function ( $task ) {
				return 'low' === $task['priority'];
			}
		);
		?>
		<h2 class="prpl-widget-title">
			<?php esc_html_e( 'Suggested tasks', 'progress-planner' ); ?>
		</h2>

		<?php foreach ( $tasks as $priority => $priority_tasks ) : ?>
			<?php if ( empty( $priority_tasks ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>

			<?php if ( 'high' === $priority ) : ?>
				<h3 class="prpl-suggested-tasks-priority-title">
					<?php esc_html_e( 'High priority', 'progress-planner' ); ?>
				</h3>
			<?php elseif ( 'medium' === $priority ) : ?>
				<h3 class="prpl-suggested-tasks-priority-title">
					<?php esc_html_e( 'Medium priority', 'progress-planner' ); ?>
				</h3>
			<?php elseif ( 'low' === $priority ) : ?>
				<h3 class="prpl-suggested-tasks-priority-title">
					<?php esc_html_e( 'Low priority', 'progress-planner' ); ?>
				</h3>
			<?php endif; ?>

			<?php foreach ( $priority_tasks as $task_id => $task ) : ?>
				<div class="prpl-suggested-task prpl-suggested-task-<?php echo esc_attr( $task_id ); ?>">
					<h4 class="prpl-suggested-task-title">
						<?php echo esc_html( $task['title'] ); ?>
					</h4>
					<p class="prpl-suggested-task-description">
						<?php echo esc_html( $task['description'] ); ?>
					</p>
					<button
						type="button"
						class="prpl-suggested-task-button"
						data-task="<?php esc_attr( $task_id ); ?>"
						data-action="add-todo"
					>
						<?php esc_html_e( 'Add to my to-do list', 'progress-planner' ); ?>
					</button>
					<button
						type="button"
						class="prpl-suggested-task-button"
						data-task="<?php esc_attr( $task_id ); ?>"
						data-action="dismiss"
					>
						<?php esc_html_e( 'Dismiss', 'progress-planner' ); ?>
					</button>
				</div>
			<?php endforeach; ?>
		<?php endforeach; ?>

		<?php
	}

	/**
	 * Get the premium to-do items.
	 *
	 * @return array
	 */
	public function get_tasks() {
		// Check if we have a cached response.
		$items = \get_transient( 'progress_planner_suggested_tasks' );

		// If we have a cached response, return it.
		if ( $items ) {
			return $items;
		}

		$remote_url = self::REMOTE_DOMAIN . '/wp-json/progress-planner-saas/v1/suggested-todo/';

		// Get the response from the remote server.
		$response = \wp_remote_get( $remote_url );

		// Bail if the request failed.
		if ( \is_wp_error( $response ) ) {
			return [];
		}

		// Get the body of the response.
		$body = \wp_remote_retrieve_body( $response );

		// Bail if the body is empty.
		if ( empty( $body ) ) {
			return [];
		}

		// Decode the JSON body.
		$data = \json_decode( $body, true );

		// Bail if the JSON decoding failed.
		if ( ! \is_array( $data ) ) {
			return [];
		}

		// Cache the response for 1 day.
		\set_transient( 'progress_planner_suggested_tasks', $data, DAY_IN_SECONDS );

		return $data;
	}
}
