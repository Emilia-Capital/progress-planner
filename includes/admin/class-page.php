<?php
/**
 * Create the admin page.
 */

namespace ProgressPlanner\Admin;

/**
 * Admin page class.
 */
class Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register the hooks.
	 */
	private function register_hooks() {
		\add_action( 'admin_menu', [ $this, 'add_page' ] );
	}

	/**
	 * Add the admin page.
	 */
	public function add_page() {
		\add_menu_page(
			\esc_html__( 'Progress Planner', 'progress-planner' ),
			\esc_html__( 'Progress Planner', 'progress-planner' ),
			'manage_options',
			'progress-planner',
			[ $this, 'render_page' ],
			'dashicons-chart-line'
		);
	}

	/**
	 * Render the admin page.
	 */
	public function render_page() {
		include PROGRESS_PLANNER_DIR . '/views/admin-page.php';
	}
}
