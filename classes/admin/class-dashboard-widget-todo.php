<?php
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

use Progress_Planner\Admin\Dashboard_Widget;
use Progress_Planner\Admin\Page;
use Progress_Planner\Widgets\ToDo;

/**
 * Class Dashboard_Widget
 */
class Dashboard_Widget_Todo extends Dashboard_Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'progress_planner_dashboard_widget_todo';

	/**
	 * Get the title of the widget.
	 *
	 * @return string
	 */
	protected function get_title() {
		return \esc_html__( 'To-do list Progress Planner', 'progress-planner' );
	}

	/**
	 * Render the dashboard widget.
	 *
	 * @return void
	 */
	public function render_widget() {
		Page::enqueue_styles();
		Page::register_scripts();
		\wp_enqueue_script( 'progress-planner-todo' );

		?>
		<div id="prpl-dashboard-widget-todo-header">
			<img src="<?php echo esc_attr( PROGRESS_PLANNER_URL . '/assets/images/icon_progress_planner.svg' ); ?>" style="width:2.5em;" alt="" />
			<p><?php esc_html_e( 'Keep track of all your tasks and make sure your site is up-to-date!', 'progress-planner' ); ?></p>
		</div>
		<?php

		( new ToDo() )->the_todo_list();
	}
}
