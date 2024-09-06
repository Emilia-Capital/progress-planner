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
final class ToDo extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'todo';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		?>
		<h2 class="prpl-widget-title">
			<?php \esc_html_e( 'To-do list', 'progress-planner' ); ?>
		</h2>
		<?php
		self::print_content();
	}

	/**
	 * Print the widget content.
	 *
	 * @return void
	 */
	public static function print_content() {
		?>
		<p>
			<?php \esc_html_e( 'Write down all your website maintenance tasks you want to get done!', 'progress-planner' ); ?>
		</p>
		<?php
		self::the_todo_list();
	}

	/**
	 * The TODO list.
	 *
	 * @return void
	 */
	public static function the_todo_list() {
		?>
		<div id="todo-aria-live-region" aria-live="polite" style="position: absolute; left: -9999px;"></div>

		<ul id="todo-list" class="prpl-todo-list"></ul>

		<form id="create-todo-item">
			<input type="text" id="new-todo-content" placeholder="<?php esc_attr_e( 'Add a new task', 'progress-planner' ); ?>" aria-label="<?php esc_attr_e( 'Add a new task', 'progress-planner' ); ?>" required />
			<button type="submit" title="<?php esc_attr_e( 'Add', 'progress-planner' ); ?>">
				<span class="dashicons dashicons-plus-alt2"></span>
			</button>
		</form>
		<?php
	}
}
