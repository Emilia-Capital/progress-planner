<?php
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Admin;

/**
 * Class Dashboard_Widget
 */
class Dashboard_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
	}

	/**
	 * Add the dashboard widget.
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'prpl_dashboard_widget',
			esc_html__( 'Progress Planner', 'progress-planner' ),
			[ $this, 'render_dashboard_widget' ]
		);
	}

	/**
	 * Render the dashboard widget.
	 */
	public function render_dashboard_widget() {
		?>
		<div class="prpl-dashboard-widget">
			<?php if ( \ProgressPlanner\Admin\Page::get_params()['scan_pending'] ) : ?>
				<?php include PROGRESS_PLANNER_DIR . '/views/admin-page-form-scan.php'; ?>
			<?php else : ?>
				<?php include PROGRESS_PLANNER_DIR . '/views/admin-page-streak.php'; ?>
				<?php include PROGRESS_PLANNER_DIR . '/views/admin-page-posts-count-progress.php'; ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
