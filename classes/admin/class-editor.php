<?php
/**
 * Tweaks for the editor.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

/**
 * Editor class.
 */
class Editor {

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_script' ] );
	}

	/**
	 * Enqueue the editor script.
	 *
	 * @return void
	 */
	public function enqueue_editor_script() {
		// Bail early when we're on the site-editor.php page.
		$request = \filter_input( INPUT_SERVER, 'REQUEST_URI' );
		if ( false !== \strpos( (string) $request, '/site-editor.php' ) ) {
			return;
		}

		\wp_enqueue_script(
			'progress-planner-editor',
			\plugins_url( '/assets/js/editor.js', PROGRESS_PLANNER_FILE ),
			[ 'wp-plugins', 'wp-edit-post', 'wp-element' ],
			(string) filemtime( \plugin_dir_path( PROGRESS_PLANNER_FILE ) . 'assets/js/editor.js' ),
			true
		);

		\wp_localize_script(
			'progress-planner-editor',
			'progressPlannerEditor',
			[
				'lessons'         => \progress_planner()->get_lessons()->get_remote_api_items(),
				'pageTypes'       => \progress_planner()->get_page_types()->get_page_types(),
				'defaultPageType' => \progress_planner()->get_page_types()->get_default_page_type( (string) \get_post_type(), (int) \get_the_ID() ),
				'i18n'            => [
					'pageType'               => \esc_html__( 'Page type', 'progress-planner' ),
					'progressPlannerSidebar' => \esc_html__( 'Progress Planner Sidebar', 'progress-planner' ),
					'progressPlanner'        => \esc_html__( 'Progress Planner', 'progress-planner' ),
				],
			]
		);
		\wp_enqueue_style(
			'progress-planner-editor',
			\plugins_url( '/assets/css/editor.css', PROGRESS_PLANNER_FILE ),
			[],
			(string) filemtime( PROGRESS_PLANNER_DIR . '/assets/css/editor.css' )
		);
	}
}