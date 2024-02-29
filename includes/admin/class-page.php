<?php
/**
 * Create the admin page.
 *
 * @package ProgressPlanner
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
	 *
	 * @return void
	 */
	private function register_hooks() {
		\add_action( 'admin_menu', [ $this, 'add_page' ] );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Add the admin page.
	 *
	 * @return void
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
	 *
	 * @return void
	 */
	public function render_page() {
		include PROGRESS_PLANNER_DIR . '/views/admin-page.php';
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'toplevel_page_progress-planner' !== $hook ) {
			return;
		}

		\wp_enqueue_script(
			'progress-planner-admin',
			PROGRESS_PLANNER_URL . 'assets/js/admin.js',
			[],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/admin.js' ),
			true
		);

		// Localize the script.
		\wp_localize_script(
			'progress-planner-admin',
			'progressPlanner',
			[
				'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
				'nonce'   => \wp_create_nonce( 'progress_planner_scan' ),
				'l10n'    => [
					'resettingStats' => \esc_html__( 'Resetting stats...', 'progress-planner' ),
				],
			]
		);

		\wp_enqueue_style(
			'progress-planner-admin',
			PROGRESS_PLANNER_URL . 'assets/css/admin.css',
			[],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/css/admin.css' )
		);
	}
}
