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
		\add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
	}

	/**
	 * Add the dashboard widget.
	 */
	public function add_dashboard_widget() {
		\wp_add_dashboard_widget(
			'prpl_dashboard_widget',
			\esc_html__( 'Progress Planner', 'progress-planner' ),
			[ $this, 'render_dashboard_widget' ]
		);
	}

	/**
	 * Render the dashboard widget.
	 */
	public function render_dashboard_widget() {
		// Enqueue Chart.js.
		// TODO: Use a local copy of Chart.js and properly enqueue it.
		echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
		?>
		<div class="prpl-dashboard-widget">
			<?php include PROGRESS_PLANNER_DIR . '/views/widgets/activity-scores.php'; ?>
			<a href="<?php echo \esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) ); ?>">
				<?php \esc_html_e( 'See more details', 'progress-planner' ); ?>
			</a>
		</div>
		<?php
	}
}
