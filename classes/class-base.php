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
	 * Magic method to get properties.
	 * We use this to avoid a lot of code duplication.
	 *
	 * If we call $this->get_settings(), we need to check if $this->settings exists.
	 * If it exists and is null, we create a new instance of the `Settings` class.
	 *
	 * @param string $name The name of the property.
	 * @param array  $arguments The arguments passed to the class constructor.
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		$map           = [
			'popovers'      => [
				'badges'   => '\Progress_Planner\Popovers\Badges',
				'settings' => '\Progress_Planner\Popovers\Settings',
			],
			'settings_page' => '\Progress_Planner\Admin\Page_Settings',
			'helpers'       => [
				'content' => '\Progress_Planner\Activities\Content_Helpers',
			],
			'admin'         => [],
			'actions'       => [],
		];
		$property_name = str_replace( 'get_', '', $name );
		if ( ! property_exists( $this, $property_name ) ) {
			return null;
		}

		if ( is_null( $this->$property_name ) ) {
			if ( isset( $map[ $property_name ] ) ) {
				if ( is_array( $map[ $property_name ] ) ) {
					$this->$property_name = new \stdClass();
					foreach ( $map[ $property_name ] as $key => $class_name ) {
						if ( class_exists( $class_name ) ) {
							$this->$property_name->$key = new $class_name( $arguments );
						}
					}
				} elseif ( class_exists( $map[ $property_name ] ) ) {
					$this->$property_name = new $map[ $property_name ]( $arguments );
				}
			} else {
				$class_name = 'Progress_Planner\\' . implode( '_', array_map( 'ucfirst', explode( '_', $property_name ) ) );
				if ( class_exists( $class_name ) ) {
					$this->$property_name = new $class_name( $arguments );
				}
			}
		}
		return $this->$property_name;
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
