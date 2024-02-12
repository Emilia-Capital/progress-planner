<?php
/**
 * Create the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Admin;

use PROGRESS_PLANNER_URL;

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
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		\add_action( 'wp_ajax_progress_planner_scan_posts', [ $this, 'ajax_scan' ] );
		\add_action( 'wp_ajax_progress_planner_reset_stats', [ $this, 'ajax_reset_stats' ] );
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

	/**
	 * Enqueue scripts and styles.
	 *
	 * @param string $hook The current admin page.
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

	/**
	 * Ajax scan.
	 */
	public function ajax_scan() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner_scan', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		// Scan the posts.
		$prepopulate = new \ProgressPlanner\Stats\Stat_Posts_Prepopulate();
		$prepopulate->prepopulate();

		// Get the last scanned post ID.
		$last_scanned_id = $prepopulate->get_last_prepopulated_post();

		// Get the last post-ID that exists on the site.
		$last_post_id = $prepopulate->get_last_post_id();

		\wp_send_json_success(
			[
				'lastScanned' => $last_scanned_id,
				'lastPost'    => $last_post_id,
				'progress'    => round( ( $last_scanned_id / $last_post_id ) * 100 ),
				'messages'    => [
					'scanComplete' => \esc_html__( 'Scan complete.', 'progress-planner' ),
				],
			]
		);
	}

	/**
	 * Ajax reset stats.
	 */
	public function ajax_reset_stats() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner_scan', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		// Reset the stats.
		$stats = new \ProgressPlanner\Stats\Stat_Posts();
		$stats->reset_stats();

		\wp_send_json_success(
			[
				'message' => \esc_html__( 'Stats reset. Refreshing the page...', 'progress-planner' ),
			]
		);
	}
}
