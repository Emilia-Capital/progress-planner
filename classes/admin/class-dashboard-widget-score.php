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
 * Class Dashboard_Widget
 */
class Dashboard_Widget_Score extends Dashboard_Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'progress_planner_dashboard_widget_score';

	/**
	 * Get the title of the widget.
	 *
	 * @return string
	 */
	protected function get_title() {
		return \esc_html__( 'Progress Planner site score', 'progress-planner' );
	}

	/**
	 * Render the dashboard widget.
	 *
	 * @return void
	 */
	public function render_widget() {
		Page::enqueue_styles();
		echo '<div class="prpl-dashboard-widget">';
		\Progress_Planner\Widgets\Website_Activity_Score::print_score_gauge();

		echo '<a href="' . \esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) ) . '">';
		\esc_html_e( 'See more details', 'progress-planner' );
		echo '</a>';
		echo '</div>';
	}
}
