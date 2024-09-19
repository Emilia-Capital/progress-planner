<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Suggested_Tasks as Root_Suggested_Tasks;

/**
 * Published Content Widget.
 */
final class Suggested_Tasks extends Widget {

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
		$api_tasks = Root_Suggested_Tasks::get_tasks();

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

		<ul>
			<?php foreach ( $tasks as $priority => $priority_tasks ) : ?>
				<?php foreach ( $priority_tasks as $task_id => $task ) : ?>
					<?php
					$classes   = [ 'prpl-suggested-task' ];
					$classes[] = 'prpl-suggested-task-priority-' . $priority;
					$classes[] = 'prpl-suggested-task-' . $task_id;
					if ( \in_array( $task_id, Root_Suggested_Tasks::get_dismissed_tasks(), true ) ) {
						$classes[] = 'prpl-suggested-task-dismissed';
					}
					$remind_in = null;
					foreach ( Root_Suggested_Tasks::get_snoozed_tasks() as $snoozed_task ) {
						if ( $task_id === $snoozed_task['id'] ) {
							$classes[] = 'prpl-suggested-task-snoozed';
							$remind_in = round( $snoozed_task['time'] - \time() ) / \DAY_IN_SECONDS;
						}
						break;
					}
					?>
					<li class="<?php echo esc_attr( \implode( ' ', $classes ) ); ?>">
						<details>
							<summary>
								<?php echo esc_html( $task['title'] ); ?>
							</summary>
							<p class="prpl-suggested-task-description">
								<?php echo esc_html( $task['description'] ); ?>
							</p>
							<p class="prpl-suggested-task-priority">
								<?php
								printf(
									/* translators: %s: priority */
									esc_html__( 'Priority: %s', 'progress-planner' ),
									esc_html( $task['priority'] )
								);
								?>
							</p>
							<?php if ( null !== $remind_in ) : ?>
								<p class="prpl-suggested-task-remind-in">
									<?php if ( 0 === (int) $remind_in ) : ?>
										<?php esc_html_e( 'Less than a day', 'progress-planner' ); ?>
									<?php else : ?>
										<?php
										printf(
											/* translators: %d: number of days */
											\esc_html__( 'Snoozed. Remaining time: %d days', 'progress-planner' ),
											(int) $remind_in
										);
										?>
									<?php endif; ?>
								</p>
							<?php endif; ?>
							<button
								type="button"
								class="button prpl-suggested-task-button"
								data-task-id="<?php echo esc_attr( $task_id ); ?>"
								data-task-title="<?php echo esc_attr( $task['title'] ); ?>"
								data-action="add-todo"
							>
								<?php esc_html_e( 'Add to my to-do list', 'progress-planner' ); ?>
							</button>
							<button
								type="button"
								class="button prpl-suggested-task-button"
								data-task-id="<?php echo esc_attr( $task_id ); ?>"
								data-task-title="<?php echo esc_attr( $task['title'] ); ?>"
								data-action="dismiss"
							>
								<?php esc_html_e( 'Dismiss', 'progress-planner' ); ?>
							</button>
							<button
								type="button"
								class="button prpl-suggested-task-button"
								data-task-id="<?php echo esc_attr( $task_id ); ?>"
								data-task-title="<?php echo esc_attr( $task['title'] ); ?>"
								data-action="snooze"
							>
								<?php esc_html_e( 'Snooze for a week', 'progress-planner' ); ?>
							</button>
						</details>
					</li>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
