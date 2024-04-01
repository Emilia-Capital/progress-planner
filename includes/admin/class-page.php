<?php
/**
 * Create the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Admin;

use ProgressPlanner\Onboard;

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
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
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
	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_progress-planner' !== $hook ) {
			return;
		}

		self::enqueue_scripts();
		self::enqueue_styles();
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {
		// Enqueue Chart.js.
		\wp_enqueue_script(
			'chart-js',
			PROGRESS_PLANNER_URL . '/assets/js/chart.min.js',
			[],
			'4.4.2',
			false
		);

		// Enqueue the ajax-request helper.
		\wp_enqueue_script(
			'progress-planner-ajax',
			PROGRESS_PLANNER_URL . '/assets/js/ajax-request.js',
			[],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/ajax-request.js' ),
			true
		);

		// Enqueue the admin script to scan posts.
		\wp_enqueue_script(
			'progress-planner-admin',
			PROGRESS_PLANNER_URL . '/assets/js/scan-posts.js',
			[ 'progress-planner-ajax' ],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/scan-posts.js' ),
			true
		);

		// Enqueue the admin script to handle onboarding.
		\wp_enqueue_script(
			'progress-planner-onboard',
			PROGRESS_PLANNER_URL . '/assets/js/onboard.js',
			[ 'progress-planner-ajax' ],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/onboard.js' ),
			true
		);

		// Localize the script.
		\wp_localize_script(
			'progress-planner-admin',
			'progressPlanner',
			[
				'onboardGetNonceURL' => Onboard::get_remote_nonce_url(),
				'onboardAPIUrl'      => Onboard::get_remote_url(),
				'ajaxUrl'            => \admin_url( 'admin-ajax.php' ),
				'nonce'              => \wp_create_nonce( 'progress_planner_scan' ),
				'l10n'               => [
					'resettingStats' => \esc_html__( 'Resetting stats...', 'progress-planner' ),
				],
			]
		);
	}

	/**
	 * Enqueue styles.
	 *
	 * @return void
	 */
	public static function enqueue_styles() {
		\wp_enqueue_style(
			'progress-planner-admin',
			PROGRESS_PLANNER_URL . '/assets/css/admin.css',
			[],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/css/admin.css' )
		);
	}
}
