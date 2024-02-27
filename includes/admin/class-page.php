<?php
/**
 * Create the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Admin;

use ProgressPlanner\Activities\Query;

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
		\add_action( 'wp_ajax_progress_planner_scan_posts', [ $this, 'ajax_scan' ] );
		\add_action( 'wp_ajax_progress_planner_reset_stats', [ $this, 'ajax_reset_stats' ] );
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

	/**
	 * Ajax scan.
	 *
	 * @return void
	 */
	public function ajax_scan() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner_scan', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		// Scan the posts.
		$updated_stats = \ProgressPlanner\Scan\Posts::update_stats();

		\wp_send_json_success(
			[
				'lastScanned' => $updated_stats['lastScannedPage'],
				'lastPage'    => $updated_stats['lastPage'],
				'progress'    => $updated_stats['progress'],
				'messages'    => [
					'scanComplete' => \esc_html__( 'Scan complete.', 'progress-planner' ),
				],
			]
		);
	}

	/**
	 * Ajax reset stats.
	 *
	 * @return void
	 */
	public function ajax_reset_stats() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner_scan', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		// Reset the stats.
		\ProgressPlanner\Scan\Posts::reset_stats();

		\wp_send_json_success(
			[
				'message' => \esc_html__( 'Stats reset. Refreshing the page...', 'progress-planner' ),
			]
		);
	}

	/**
	 * Get params for the admin page.
	 *
	 * @return array The params.
	 */
	public static function get_params() {
		return [
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			'filter_interval' => isset( $_POST['interval'] ) ? sanitize_key( $_POST['interval'] ) : 'weeks',
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			'filter_number'   => isset( $_POST['number'] ) ? (int) $_POST['number'] : 10,
			'scan_pending'    => empty(
				Query::get_instance()->query_activities(
					[
						'category' => 'post',
						'type'     => 'publish',
					]
				)
			),
		];
	}

	/**
	 * Get total number of published posts.
	 *
	 * @return int
	 */
	public static function get_posts_published_all() {
		$activities = Query::get_instance()->query_activities(
			[
				'category' => 'post',
				'type'     => 'publish',
				'data'     => [
					'post_type' => 'post',
				],
			]
		);

		return count( $activities );
	}

	/**
	 * Get number of posts published in the past week.
	 *
	 * @return int
	 */
	public static function get_posts_published_this_week() {
		$activities = Query::get_instance()->query_activities(
			[
				'category'   => 'post',
				'type'       => 'publish',
				'start_date' => new \DateTime( '-7 days' ),
				'end_date'   => new \DateTime( 'now' ),
				'data'       => [
					'post_type' => 'post',
				],
			]
		);

		return count( $activities );
	}
}
