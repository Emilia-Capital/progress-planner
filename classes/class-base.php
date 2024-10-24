<?php
/**
 * Progress Planner main plugin class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

/**
 * Main plugin class.
 */
class Base {

	/**
	 * The target score.
	 *
	 * @var int
	 */
	const SCORE_TARGET = 200;

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
	 * An instance of the \Progress_Planner\Todo class.
	 *
	 * @var \Progress_Planner\Todo|null
	 */
	private $todo;

	/**
	 * An object containing all popovers.
	 *
	 * @var \stdClass|null
	 */
	private $popovers;

	/**
	 * An object containing all badges.
	 *
	 * @var \Progress_Planner\Badges|null
	 */
	private $badges;

	/**
	 * An instance of the \Progress_Planner\Admin\Page_Settings class.
	 *
	 * @var \Progress_Planner\Admin\Page_Settings|null
	 */
	private $settings_page;

	/** An object containing helper classes.
	 *
	 * @var \stdClass|null
	 */
	private $helpers;

	/**
	 * An object containing admin classes.
	 *
	 * @var \stdClass|null
	 */
	private $admin;

	/**
	 * An instance of the \Progress_Planner\Onboard class.
	 *
	 * @var \Progress_Planner\Onboard|null
	 */
	private $onboard;

	/**
	 * An object containing actions classes.
	 *
	 * @var \stdClass|null
	 */
	private $actions;

	/**
	 * An instance of the \Progress_Planner\Rest_API class.
	 *
	 * @var \Progress_Planner\Rest_API|null
	 */
	private $rest_api;

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
			$this->get_admin()->page                     = new \Progress_Planner\Admin\Page();
			$this->get_admin()->tour                     = new \Progress_Planner\Admin\Tour();
			$this->get_admin()->dashboard_widgets        = new \stdClass();
			$this->get_admin()->dashboard_widgets->score = new \Progress_Planner\Admin\Dashboard_Widget_Score();
			$this->get_admin()->dashboard_widgets->todo  = new \Progress_Planner\Admin\Dashboard_Widget_Todo();
		}
		$this->get_admin()->editor = new \Progress_Planner\Admin\Editor();

		$this->actions               = new \stdClass();
		$this->actions->content      = new \Progress_Planner\Actions\Content();
		$this->actions->content_scan = new \Progress_Planner\Actions\Content_Scan();
		$this->actions->maintenance  = new \Progress_Planner\Actions\Maintenance();

		// REST API.
		$this->rest_api = new Rest_API();

		// Onboarding.
		$this->onboard = new Onboard();

		// To-do.
		$this->todo = new Todo();

		\add_filter( 'plugin_action_links_' . plugin_basename( PROGRESS_PLANNER_FILE ), [ $this, 'add_action_links' ] );

		// We need to initialize some classes early.
		$this->page_types      = new Page_Types();
		$this->settings        = new Settings();
		$this->settings_page   = new \Progress_Planner\Admin\Page_Settings();
		$this->suggested_tasks = new Suggested_Tasks();
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
	 * Get the todo instance.
	 *
	 * @return \Progress_Planner\Todo
	 */
	public function get_todo() {
		if ( ! $this->todo ) {
			$this->todo = new Todo();
		}
		return $this->todo;
	}

	/**
	 * Get the popovers instance.
	 *
	 * @return \stdClass
	 */
	public function get_popovers() {
		if ( ! $this->popovers ) {
			$this->popovers = new \stdClass();
		}
		$this->popovers->badges   = new \Progress_Planner\Popovers\Badges();
		$this->popovers->settings = new \Progress_Planner\Popovers\Settings();

		return $this->popovers;
	}

	/**
	 * Get the badges instance.
	 *
	 * @return \Progress_Planner\Badges
	 */
	public function get_badges() {
		if ( ! $this->badges ) {
			$this->badges = new Badges();
		}
		return $this->badges;
	}

	/**
	 * Get the settings page instance.
	 *
	 * @return \Progress_Planner\Admin\Page_Settings
	 */
	public function get_settings_page() {
		if ( ! $this->settings_page ) {
			$this->settings_page = new \Progress_Planner\Admin\Page_Settings();
		}
		return $this->settings_page;
	}

	/** Get the helpers instance.
	 *
	 * @return \stdClass
	 */
	public function get_helpers() {
		if ( ! $this->helpers ) {
			$this->helpers          = new \stdClass();
			$this->helpers->content = new \Progress_Planner\Activities\Content_Helpers();
		}
		return $this->helpers;
	}

	/**
	 * Get the admin instance.
	 *
	 * @return \stdClass
	 */
	public function get_admin() {
		if ( ! $this->admin ) {
			$this->admin = new \stdClass();
		}
		return $this->admin;
	}

	/**
	 * Get the onboard instance.
	 *
	 * @return \Progress_Planner\Onboard
	 */
	public function get_onboard() {
		if ( ! $this->onboard ) {
			$this->onboard = new Onboard();
		}
		return $this->onboard;
	}

	/**
	 * Get the actions instance.
	 *
	 * @return \stdClass
	 */
	public function get_actions() {
		if ( ! $this->actions ) {
			$this->actions = new \stdClass();
		}
		return $this->actions;
	}

	/**
	 * Get the rest api instance.
	 *
	 * @return \Progress_Planner\Rest_API
	 */
	public function get_rest_api() {
		if ( ! $this->rest_api ) {
			$this->rest_api = new Rest_API();
		}
		return $this->rest_api;
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
}
