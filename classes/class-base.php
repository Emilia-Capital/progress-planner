<?php
/**
 * Progress Planner main plugin class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Admin\Page as Admin_page;
use Progress_Planner\Admin\Dashboard_Widget_Score;
use Progress_Planner\Admin\Tour;
use Progress_Planner\Admin\Dashboard_Widget_Todo;
use Progress_Planner\Actions\Content as Actions_Content;
use Progress_Planner\Actions\Content_Scan as Actions_Content_Scan;
use Progress_Planner\Actions\Maintenance as Actions_Maintenance;
use Progress_Planner\Badges\Badge\Wonderful_Writer as Badge_Wonderful_Writer;
use Progress_Planner\Badges\Badge\Bold_Blogger as Badge_Bold_Blogger;
use Progress_Planner\Badges\Badge\Awesome_Author as Badge_Awesome_Author;
use Progress_Planner\Badges\Badge\Progress_Padawan as Badge_Progress_Padawan;
use Progress_Planner\Badges\Badge\Maintenance_Maniac as Badge_Maintenance_Maniac;
use Progress_Planner\Badges\Badge\Super_Site_Specialist as Badge_Super_Site_Specialist;
use Progress_Planner\Rest_API;
use Progress_Planner\Todo;
use Progress_Planner\Suggested_Tasks;

/**
 * Main plugin class.
 */
class Base {

	/**
	 * An instance of the \Progress_Planner\Settings class.
	 *
	 * @var \Progress_Planner\Settings|null
	 */
	private $settings;

	/**
	 * An instance of the Query class.
	 *
	 * @var \Progress_Planner\Query|null
	 */
	private $query;

	/**
	 * An instance of the \Progress_Planner\Date class.
	 *
	 * @var \Progress_Planner\Date|null
	 */
	private $date;

	/**
	 * An instance of the \Progress_Planner\Lessons class.
	 *
	 * @var \Progress_Planner\Lessons|null
	 */
	private $lessons;

	/**
	 * An instance of the \Progress_Planner\Page_Types class.
	 *
	 * @var \Progress_Planner\Page_Types|null
	 */
	private $page_types;

	/**
	 * An instance of the \Progress_Planner\Chart class.
	 *
	 * @var \Progress_Planner\Chart|null
	 */
	public $chart;

	/**
	 * An instance of the \Progress_Planner\Suggested_Tasks class.
	 *
	 * @var \Progress_Planner\Suggested_Tasks|null
	 */
	private $suggested_tasks;

	/**
	 * An array of configuration values for points awarded by action-type.
	 *
	 * @var array
	 */
	public static $points_config = [
		'content'      => [
			'publish'          => 50,
			'update'           => 10,
			'delete'           => 5,
			'word-multipliers' => [
				100  => 1.1,
				350  => 1.25,
				1000 => 0.8,
			],
		],
		'maintenance'  => 10,
		'todo'         => [
			'add'     => 1,
			'delete'  => 1,
			'update'  => 3, // Handles marking as done, and updating the content.
			'default' => 1,
		],
		'score-target' => 200,
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! function_exists( 'current_user_can' ) ) {
			require_once ABSPATH . 'wp-includes/capabilities.php';
		}
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			require_once ABSPATH . 'wp-includes/pluggable.php';
		}

		if ( defined( '\IS_PLAYGROUND_PREVIEW' ) && constant( '\IS_PLAYGROUND_PREVIEW' ) === true ) {
			new Playground();
		}

		// Basic classes.
		if ( \is_admin() && \current_user_can( 'publish_posts' ) ) {
			new Admin_Page();
			new Tour();
			new Dashboard_Widget_Score();
			new Dashboard_Widget_Todo();
			new Page_Types();
		}
		new Actions_Content();
		new Actions_Maintenance();
		new Actions_Content_Scan();

		// Content badges.
		new Badge_Wonderful_Writer();
		new Badge_Bold_Blogger();
		new Badge_Awesome_Author();

		// Maintenance badges.
		new Badge_Progress_Padawan();
		new Badge_Maintenance_Maniac();
		new Badge_Super_Site_Specialist();

		// REST API.
		new Rest_API();

		// Onboarding.
		new Onboard();

		// To-do.
		new Todo();

		\add_filter( 'plugin_action_links_' . plugin_basename( PROGRESS_PLANNER_FILE ), [ $this, 'add_action_links' ] );
		\add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_script' ] );
	}

	/**
	 * Get the settings instance.
	 *
	 * @return \Progress_Planner\Settings
	 */
	public function get_settings() {
		if ( ! $this->settings ) {
			$this->settings = new Settings();
		}
		return $this->settings;
	}

	/**
	 * Get the query instance.
	 *
	 * @return \Progress_Planner\Query
	 */
	public function get_query() {
		if ( ! $this->query ) {
			$this->query = new Query();
		}
		return $this->query;
	}

	/**
	 * Get the date instance.
	 *
	 * @return \Progress_Planner\Date
	 */
	public function get_date() {
		if ( ! $this->date ) {
			$this->date = new Date();
		}
		return $this->date;
	}

	/**
	 * Get the lessons instance.
	 *
	 * @return \Progress_Planner\Lessons
	 */
	public function get_lessons() {
		if ( ! $this->lessons ) {
			$this->lessons = new Lessons();
		}
		return $this->lessons;
	}

	/**
	 * Get the page types instance.
	 *
	 * @return \Progress_Planner\Page_Types
	 */
	public function get_page_types() {
		if ( ! $this->page_types ) {
			$this->page_types = new Page_Types();
		}
		return $this->page_types;
	}

	/**
	 * Get the chart instance.
	 *
	 * @return \Progress_Planner\Chart
	 */
	public function get_chart() {
		if ( ! $this->chart ) {
			$this->chart = new Chart();
		}
		return $this->chart;
	}

	/**
	 * Get the suggested tasks instance.
	 *
	 * @return \Progress_Planner\Suggested_Tasks
	 */
	public function get_suggested_tasks() {
		if ( ! $this->suggested_tasks ) {
			$this->suggested_tasks = new Suggested_Tasks();
		}
		return $this->suggested_tasks;
	}

	/**
	 * Get the activation date.
	 *
	 * @return \DateTime
	 */
	public function get_activation_date() {
		$activation_date = $this->get_settings()->get( 'activation_date' );
		if ( ! $activation_date ) {
			$activation_date = new \DateTime();
			$this->get_settings()->set( 'activation_date', $activation_date->format( 'Y-m-d' ) );
			return $activation_date;
		}
		return \DateTime::createFromFormat( 'Y-m-d', $activation_date );
	}

	/**
	 * Add action link to dashboard page.
	 *
	 * @param array $actions Existing actions.
	 *
	 * @return array
	 */
	public function add_action_links( $actions ) {
		$action_link = [ '<a href="' . admin_url( 'admin.php?page=progress-planner' ) . '">' . __( 'Dashboard', 'progress-planner' ), '</a>' ];
		$actions     = array_merge( $action_link, $actions );
		return $actions;
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
				'lessons'         => $this->get_lessons()->get_remote_api_items(),
				'pageTypes'       => $this->get_page_types()->get_page_types(),
				'defaultPageType' => $this->get_page_types()->get_default_page_type( (string) \get_post_type(), (int) \get_the_ID() ),
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
