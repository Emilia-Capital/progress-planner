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
		return \esc_html__( 'Progress Planner to-do tasks', 'progress-planner' );
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

		ToDo::print_content();
	}
}
