<?php // phpcs:disable Generic.Commenting.Todo
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
	 * An array of instantiated objects.
	 *
	 * @var array<string, object>
	 */
	private $cached = [];

	/**
	 * An object containing all popovers.
	 *
	 * @var \stdClass|null
	 */
	private $popovers;

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
	 * An object containing actions classes.
	 *
	 * @var \stdClass|null
	 */
	private $actions;

	/**
	 * An instance of the \Progress_Planner\Cache class.
	 *
	 * @var \Progress_Planner\Cache|null
	 */
	private $cache;

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

		$this->cached['actions']               = new \stdClass();
		$this->cached['actions']->content      = new \Progress_Planner\Actions\Content();
		$this->cached['actions']->content_scan = new \Progress_Planner\Actions\Content_Scan();
		$this->cached['actions']->maintenance  = new \Progress_Planner\Actions\Maintenance();

		// REST API.
		$this->cached['rest_api'] = new Rest_API();

		// Onboarding.
		$this->cached['onboard'] = new Onboard();

		// To-do.
		$this->cached['todo'] = new Todo();

		\add_filter( 'plugin_action_links_' . plugin_basename( PROGRESS_PLANNER_FILE ), [ $this, 'add_action_links' ] );

		// We need to initialize some classes early.
		$this->cached['page_types']      = new Page_Types();
		$this->cached['settings']        = new Settings();
		$this->cached['settings_page']   = new \Progress_Planner\Admin\Page_Settings();
		$this->cached['suggested_tasks'] = new Suggested_Tasks();
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
	 * Magic method to get properties.
	 * We use this to avoid a lot of code duplication.
	 *
	 * @param string $name The name of the property.
	 * @param array  $arguments The arguments passed to the class constructor.
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		$cache_name = str_replace( 'get_', '', $name );
		if ( isset( $this->cached[ $cache_name ] ) ) {
			return $this->cached[ $cache_name ];
		}

		if ( ! isset( $this->cached[ $cache_name ] ) ) {
			$class_name = 'Progress_Planner\\' . implode( '_', array_map( 'ucfirst', explode( '_', $cache_name ) ) );
			if ( class_exists( $class_name ) ) {
				$this->cached[ $cache_name ] = new $class_name( $arguments );
				return $this->cached[ $cache_name ];
			}
		}
	}

	/**
	 * Get the cache instance.
	 *
	 * @return \Progress_Planner\Cache
	 */
	public function get_cache() {
		if ( ! $this->cache ) {
			$this->cache = new Cache();
		}
		return $this->cache;
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
	 * Include a template.
	 *
	 * @param string|array $template The template to include.
	 *                               If an array, go through each item until the template exists.
	 * @param array        $args   The arguments to pass to the template.
	 * @return void
	 */
	public function the_view( $template, $args = [] ) {
		$this->the_file( [ $template, "/views/{$template}" ], $args );
	}

	/**
	 * Include an asset.
	 *
	 * @param string|array $asset The asset to include.
	 *                            If an array, go through each item until the asset exists.
	 * @param array        $args  The arguments to pass to the template.
	 *
	 * @return void
	 */
	public function the_asset( $asset, $args = [] ) {
		$this->the_file( [ $asset, "/assets/{$asset}" ], $args );
	}

	/**
	 * Include a file.
	 *
	 * @param string|array $files The file to include.
	 *                           If an array, go through each item until the file exists.
	 * @param array        $args  The arguments to pass to the template.
	 * @return void
	 */
	public function the_file( $files, $args = [] ) {
		/**
		 * Allow filtering the files to include.
		 *
		 * @param array $files The files to include.
		 */
		$files = apply_filters( 'progress_planner_the_file', (array) $files );
		foreach ( $files as $file ) {
			$path = $file;
			if ( ! \file_exists( $path ) ) {
				$path = \PROGRESS_PLANNER_DIR . "/{$file}";
			}
			if ( \file_exists( $path ) ) {
				extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
				include $path; // phpcs:ignore PEAR.Files.IncludingFile.UseRequire
				break;
			}
		}
	}
}
// phpcs:enable Generic.Commenting.Todo
