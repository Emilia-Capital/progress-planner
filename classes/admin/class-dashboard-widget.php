<?php
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

use Progress_Planner\Admin\Page;

/**
 * Class Dashboard_Widget
 */
class Dashboard_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
	}

	/**
	 * Add the dashboard widget.
	 *
	 * @return void
	 */
	public function add_dashboard_widget() {
		\wp_add_dashboard_widget(
			'progress_planner_dashboard_widget',
			\esc_html__( 'Progress Planner', 'progress-planner' ),
			[ $this, 'render_dashboard_widget' ]
		);
	}

	/**
	 * Render the dashboard widget.
	 *
	 * @return void
	 */
	public function render_dashboard_widget() {
		Page::enqueue_styles();
		echo '<div class="prpl-dashboard-widget">';
		\Progress_Planner\Widgets\Website_Activity_Score::print_score_gauge();

		echo '<a href="' . \esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) ) . '">';
		\esc_html_e( 'See more details', 'progress-planner' );
		echo '</a>';
		echo '</div>';
	}
}
