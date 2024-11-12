<?php
/**
 * Create the admin page.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

use Progress_Planner\Onboard;

/**
 * Admin page class.
 */
class Page {

	/**
	 * The columns and widgets to display on the admin page.
	 */
	const COLUMNS = [
		'prpl-column-main prpl-column-main-primary'   => [
			'prpl-column prpl-column-first' => [
				'\Progress_Planner\Widgets\Website_Activity_Score',
				'prpl-column prpl-column-two-col' => [
					'\Progress_Planner\Widgets\Published_Content_Density',
					'\Progress_Planner\Widgets\Published_Words',
				],
				'\Progress_Planner\Widgets\Published_Content',
				'\Progress_Planner\Widgets\Whats_New',
			],
		],
		'prpl-column-main prpl-column-main-secondary' => [
			'prpl-column prpl-column-first'  => [
				'\Progress_Planner\Widgets\Activity_Scores',
				'\Progress_Planner\Widgets\Plugins',
				'\Progress_Planner\Widgets\Badges_Progress',
				'\Progress_Planner\Widgets\Personal_Record_Content',
			],
			'prpl-column prpl-column-second' => [
				'\Progress_Planner\Widgets\ToDo',
				'\Progress_Planner\Widgets\Latest_Badge',
				'\Progress_Planner\Widgets\Badge_Content',
				'\Progress_Planner\Widgets\Badge_Streak',
			],
		],
	];

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
		\add_action( 'wp_ajax_progress_planner_save_cpt_settings', [ $this, 'save_cpt_settings' ] );
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
			'data:image/svg+xml;base64,PHN2ZyByb2xlPSJpbWciIGFyaWEtaGlkZGVuPSJ0cnVlIiBmb2N1c2FibGU9ImZhbHNlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzNjggNTAwIj48cGF0aCBmaWxsPSIjMzgyOTZkIiBkPSJNMjE3LjQ2IDE3Mi45YzMuMjEuMTIgNS45NyAxLjc0IDcuNzMgNC4xNS0xLjg3LTEwLjI0LTEwLjY0LTE4LjE3LTIxLjQ4LTE4LjU2LTEyLjUyLS40NS0yMy4wMyA5LjMzLTIzLjQ4IDIxLjg1LS40NSAxMi41MiA5LjMzIDIzLjAzIDIxLjg1IDIzLjQ4IDkuNC4zNCAxNy42Ny01LjEgMjEuNC0xMy4xMy0xLjgzIDEuNTEtNC4xOCAyLjQyLTYuNzQgMi4zMy01LjU1LS4yLTkuODktNC44Ni05LjY5LTEwLjQxLjItNS41NSA0Ljg2LTkuODkgMTAuNDEtOS42OVpNMjQxLjUxIDMwNS44NGMuNTggMS45MiAxLjEzIDMuODYgMS43MyA1Ljc3IDE0LjA0IDQ0Ljk3IDMzLjk0IDg4Ljc1IDU2LjQyIDEyNC4yN2w2Ny43NS0xMzAuMDRoLTEyNS45Wk0yOTcuOTYgMjA1Ljk3YzEyLjEyLTQuNSAyMy41NC03LjE4IDMzLjY0LTguOTYtMjIuNTEtMjIuMjctNjEuMjQtMjcuMDYtNjEuNDctMjcuMDkgMS4yNyA2LjE3LjU4IDE1LjgtMi40NCAyNi40Ni0zLjMgMTEuNjYtOS4zOCAyNC41NC0xOC43IDM1LjQ4LTMuNDUgNC4wNi03LjM2IDcuODMtMTEuNzMgMTEuMTloLjA3di0uMDFjLjE2LjYyLjM4IDEuMi41OCAxLjc5IDIuNzQgOC4yNyA4LjYxIDEzLjc0IDE0LjkzIDE3LjE0IDYuNDggMy40OSAxMy4zNyA0LjgzIDE3LjY4IDQuODMgNi40IDAgMTEuODgtMy43OSAxNC40My05LjIyLjk3LTIuMDYgMS41NS00LjMzIDEuNTUtNi43NiAwLTMuODUtMS40Mi03LjM0LTMuNjktMTAuMS0xLjkyLTIuMzMtNC40Ni00LjA4LTcuMzktNS4wM2w0NC44Mi04LjY1Yy02LjYzLTYuMTItMTQuNzItMTEuNTktMjIuNzMtMTYuMjMtMS45Ny0xLjE0LTEuNjktNC4wNS40NS00Ljg0WiIvPjxwYXRoIGZpbGw9IiNmYWEzMTAiIGQ9Ik0yODEuMzcgNDU4LjM3Yy0yNS43OS0zOC44NC00OC42OC04OC4wNC02NC40NS0xMzguNTQtMS40NS00LjYzLTIuODMtOS4zMS00LjE3LTEzLjk5LTEuMTItMy45NC0yLjIyLTcuODgtMy4yNS0xMS44LTIuMDktNy45Mi05LjI4LTEzLjQ2LTE3LjQ4LTEzLjQ2aC0yNy45NWMtOC4yIDAtMTUuMzkgNS41My0xNy40OCAxMy40NS0yLjI4IDguNjUtNC43OCAxNy4zMi03LjQyIDI1Ljc5LTE1Ljc3IDUwLjUtMzguNjUgOTkuNy02NC40NSAxMzguNTQtNC4wMSA2LjAzLTEuNzggMTEuNjMtLjY0IDEzLjc2IDIuNCA0LjQ3IDYuODYgNy4xNCAxMS45NCA3LjE0aDY2LjAxbDMuOTcgNi45MmM0LjU0IDcuOSAxMi45OSAxMi44MSAyMi4wNSAxMi44MXMxNy41MS00LjkxIDIyLjA2LTEyLjgxbDMuOTgtNi45Mmg2NmMzLjIyIDAgNi4xOS0xLjA4IDguNTUtMy4wMiAxLjM1LTEuMTEgMi41MS0yLjQ5IDMuMzgtNC4xMy41Ny0xLjA3IDEuNDItMy4wMiAxLjYxLTUuNDYuMTktMi40MS0uMjYtNS4zMS0yLjI1LTguMzFaIi8+PHBhdGggZmlsbD0iIzM4Mjk2ZCIgZD0iTTI5NS43IDc2LjA2Yy03LjU0LTEyLjA1LTMyLjM4IDEtNTkuNTQgMi44Ni0xNS4wNCAxLjAzLTM3LjA1LTExMC42My03MS43Ny01Ni45OS0zOS41NiA2MS4xLTc5LjEyLTQ0LjY4LTg4LjY2LTE1LjgzLTIxLjExIDQzLjI3IDI1LjE1IDg0LjYxIDI1LjE1IDg0LjYxcy0xMi44NCA3LjkyLTIwLjYzIDEzLjkzYy01LjQ3IDQuMTctMTAuODIgOC42NS0xNi4wMyAxMy41MS0yMC40NSAxOS4wMy0zNi4wNCA0MC4zMi00Ni43NyA2My44NkM2LjcyIDIwNS41NSAxLjExIDIyOS41OS42MiAyNTQuMTVjLS40OSAyNC41NiA0LjAxIDQ5LjEgMTMuNTQgNzMuNjMgOS41MiAyNC41MyAyNC4xNyA0Ny40MiA0My45NSA2OC42OCA0LjAyIDQuMzIgOC4xMiA4LjQxIDEyLjMxIDEyLjMgNC4xLTYuMzEgNy45Ny0xMi43NCAxMS42NC0xOS4yNiA0LjM5LTcuOCA4LjUtMTUuNzIgMTIuMjUtMjMuNzgtLjMzLS4zNS0uNjYtLjY5LS45OS0xLjAzLS4xNy0uMTgtLjM0LS4zNS0uNTEtLjUzLTE1LjUzLTE2LjY5LTI3LjE3LTM0LjU5LTM0LjkzLTUzLjcyLTcuNzctMTkuMTMtMTEuNS0zOC4yNS0xMS4yLTU3LjM2LjI5LTE5LjEgNC40Ny0zNy42OCAxMi41My01NS43MiA4LjA2LTE4LjA1IDIwLjAyLTM0LjQ1IDM1LjktNDkuMjIgMTMuOTktMTMuMDIgMjguODQtMjIuODMgNDQuNTUtMjkuNDEgMTUuNy02LjU5IDMxLjYzLTkuOTggNDcuNzYtMTAuMTggOS4wNS0uMTEgMTkuMTEgMS4xNSAyOS41MSA0LjUgMTAuMzIgNC4yNyAxOS4yMiA5LjQ0IDI2LjYzIDE1LjM1IDEwLjE5IDguMTMgMTcuNjEgMTcuNjUgMjIuMjIgMjguMSAxLjkxIDQuMzIgMy4zNyA4LjggNC4zMiAxMy40MSAxNi4yNy0yOC4yNyAzNi43NS03NS45NiAyNS41Ny05My44M1oiLz48L3N2Zz4='
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public function render_page() {
		?>
		<div class="wrap prpl-wrap">
			<h1 class="screen-reader-text"><?php \esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>
			<?php require PROGRESS_PLANNER_DIR . '/views/admin-page-header.php'; ?>
			<?php require PROGRESS_PLANNER_DIR . '/views/welcome.php'; ?>

			<?php do_action( 'progress_planner_admin_after_header' ); ?>

			<div class="prpl-widgets-container">
				<?php
				$columns = apply_filters( 'progress_planner_admin_columns_widgets', self::COLUMNS );

				foreach ( $columns as $key => $value ) {
					$this->render_column( $key, $value );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a column.
	 *
	 * @param string       $key   The column key.
	 * @param string|array $value The widget(s) to render.
	 *
	 * @return void
	 */
	private function render_column( $key, $value ) {
		echo '<div class="' . \esc_attr( $key ) . '">';

		if ( \is_array( $value ) ) {
			foreach ( $value as $sub_key => $sub_value ) {
				if ( \is_string( $sub_value ) && \str_starts_with( $sub_value, '\\' ) ) {
					new $sub_value();
					continue;
				}
				$this->render_column( $sub_key, $sub_value );
			}
		} elseif ( \str_starts_with( $value, '\\' ) ) {
			new $value();
		}

		echo '</div>';
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
	 * Register scripts.
	 *
	 * @return void
	 */
	public static function register_scripts() {
		// Register Chart.js.
		\wp_register_script(
			'chart-js',
			PROGRESS_PLANNER_URL . '/assets/js/vendor/chart.min.js',
			[],
			'4.4.2',
			false
		);

		// Register the ajax-request helper.
		\wp_register_script(
			'progress-planner-ajax',
			PROGRESS_PLANNER_URL . '/assets/js/ajax-request.js',
			[],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/ajax-request.js' ),
			true
		);

		// Register the admin script to scan posts.
		\wp_register_script(
			'progress-planner-scanner',
			PROGRESS_PLANNER_URL . '/assets/js/scan-posts.js',
			[ 'progress-planner-ajax' ],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/scan-posts.js' ),
			true
		);

		// Register the admin script to handle onboarding.
		\wp_register_script(
			'progress-planner-onboard',
			PROGRESS_PLANNER_URL . '/assets/js/onboard.js',
			[ 'progress-planner-ajax', 'progress-planner-scanner' ],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/onboard.js' ),
			true
		);

		// Register the admin script for the settings popup.
		\wp_register_script(
			'progress-planner-settings',
			PROGRESS_PLANNER_URL . '/assets/js/settings.js',
			[ 'progress-planner-ajax', 'wp-util' ],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/settings.js' ),
			true
		);

		// Register the admin script for the page.
		\wp_register_script(
			'progress-planner-admin',
			PROGRESS_PLANNER_URL . '/assets/js/header-filters.js',
			[],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/header-filters.js' ),
			true
		);

		\wp_register_script(
			'progress-planner-todo',
			PROGRESS_PLANNER_URL . '/assets/js/todo.js',
			[ 'jquery-ui-sortable', 'progress-planner-ajax', 'wp-util', 'wp-a11y' ],
			filemtime( PROGRESS_PLANNER_DIR . '/assets/js/todo.js' ),
			true
		);

		$localize_data = [
			'onboardNonceURL' => Onboard::get_remote_nonce_url(),
			'onboardAPIUrl'   => Onboard::get_remote_url(),
			'ajaxUrl'         => \admin_url( 'admin-ajax.php' ),
			'nonce'           => \wp_create_nonce( 'progress_planner' ),
		];

		// Localize the scripts.
		\wp_localize_script( 'progress-planner-onboard', 'progressPlanner', $localize_data );
		\wp_localize_script( 'progress-planner-admin', 'progressPlanner', $localize_data );
		$localize_data_settings = array_merge(
			$localize_data,
			[
				'l10n' => [
					'saving' => \esc_html__( 'Saving...', 'progress-planner' ),
				],
			]
		);
		\wp_localize_script( 'progress-planner-settings', 'progressPlanner', $localize_data_settings );
		\wp_localize_script(
			'progress-planner-todo',
			'progressPlannerTodo',
			[
				'ajaxUrl'   => \admin_url( 'admin-ajax.php' ),
				'nonce'     => \wp_create_nonce( 'progress_planner_todo' ),
				'listItems' => \Progress_Planner\Todo::get_items(),
				'i18n'      => [
					'drag'             => \esc_html__( 'Drag to reorder', 'progress-planner' ),
					/* translators: %s: The task content. */
					'taskDelete'       => \esc_html__( "Delete task '%s'", 'progress-planner' ),
					/* translators: %s: The task content. */
					'taskMoveUp'       => \esc_html__( "Move task '%s' up", 'progress-planner' ),
					/* translators: %s: The task content. */
					'taskMoveDown'     => \esc_html__( "Move task '%s' down", 'progress-planner' ),
					'taskMovedUp'      => \esc_html__( 'Task moved up', 'progress-planner' ),
					'taskMovedDown'    => \esc_html__( 'Task moved down', 'progress-planner' ),
					/* translators: %s: The task content. */
					'taskCompleted'    => \esc_html__( "Task '%s' completed and moved to the bottom", 'progress-planner' ),
					/* translators: %s: The task content. */
					'taskNotCompleted' => \esc_html__( "Task '%s' marked as not completed and moved to the top", 'progress-planner' ),
				],
			]
		);
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {
		self::register_scripts();

		\wp_enqueue_script( 'chart-js' );
		\wp_enqueue_script( 'progress-planner-onboard' );
		\wp_enqueue_script( 'progress-planner-admin' );
		\wp_enqueue_script( 'progress-planner-todo' );
		\wp_enqueue_script( 'progress-planner-settings' );
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

	/**
	 * Save the post types settings.
	 *
	 * @return void
	 */
	public function save_cpt_settings() {
		\check_ajax_referer( 'progress_planner', 'nonce', false );
		$include_post_types = isset( $_POST['include_post_types'] ) ? \sanitize_text_field( \wp_unslash( $_POST['include_post_types'] ) ) : 'post,page';
		$include_post_types = \explode( ',', $include_post_types );
		\Progress_Planner\Settings::set( 'include_post_types', $include_post_types );

		\wp_send_json_success(
			[
				'message' => \esc_html__( 'Settings saved.', 'progress-planner' ),
			]
		);
	}
}
