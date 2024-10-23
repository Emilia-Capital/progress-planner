<?php
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

use Progress_Planner\Admin\Dashboard_Widget;
use Progress_Planner\Admin\Page;

/**
 * Class Dashboard_Widget_Todo
 */
class Dashboard_Widget_Todo extends Dashboard_Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'todo';

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
		\wp_enqueue_style(
			'prpl-widget-todo',
			PROGRESS_PLANNER_URL . '/assets/css/page-widgets/todo.css',
			[],
			(string) filemtime( PROGRESS_PLANNER_DIR . '/assets/css/page-widgets/todo.css' )
		);

		include \PROGRESS_PLANNER_DIR . "/views/dashboard-widgets/{$this->id}.php"; // phpcs:ignore PEAR.Files.IncludingFile.UseInclude
	}
}
