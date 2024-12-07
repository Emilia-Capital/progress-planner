<?php // phpcs:disable Generic.Commenting.Todo
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

/**
 * Class Dashboard_Widget_Todo
 */
class Dashboard_Widget_Todo extends \Progress_Planner\Admin\Dashboard_Widget {

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
		\progress_planner()->get_admin__page()->enqueue_styles();
		\progress_planner()->get_admin__scripts()->register_scripts();
		\wp_enqueue_script( 'progress-planner-todo' );
		\wp_enqueue_style(
			'prpl-widget-todo',
			PROGRESS_PLANNER_URL . '/assets/css/page-widgets/todo.css',
			[],
			\Progress_Planner\Base::get_file_version( PROGRESS_PLANNER_DIR . '/assets/css/page-widgets/todo.css' )
		);

		\progress_planner()->the_view( "dashboard-widgets/{$this->id}.php" );
	}
}
// phpcs:enable Generic.Commenting.Todo
