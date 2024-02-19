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
				<p>
					<?php
					printf(
						/* translators: %s: URL to the admin page, with text "the Progress Planner admin page" */
						esc_html__( 'You haven\'t scanned your posts yet. Please visit %s to scan your site and get started!', 'progress-planner' ),
						'<a href="' . esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) ) . '">' . esc_html__( 'the Progress Planner admin page', 'progress-planner' ) . '</a>'
					)
					?>
				</p>
			<?php else : ?>
				<?php include PROGRESS_PLANNER_DIR . '/views/admin-page-streak.php'; ?>
				<?php include PROGRESS_PLANNER_DIR . '/views/admin-page-posts-count-progress.php'; ?>
				<a href="<?php echo esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) ); ?>">
					<?php esc_html_e( 'See more details', 'progress-planner' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}
