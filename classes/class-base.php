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
			$this->cached['admin__page']                   = new \Progress_Planner\Admin\Page();
			$this->cached['admin__tour']                   = new \Progress_Planner\Admin\Tour();
			$this->cached['admin__dashboard_widget_score'] = new \Progress_Planner\Admin\Dashboard_Widget_Score();
			$this->cached['admin__dashboard_widget_todo']  = new \Progress_Planner\Admin\Dashboard_Widget_Todo();
		}
		$this->cached['admin__editor'] = new \Progress_Planner\Admin\Editor();

		$this->cached['actions__content']      = new \Progress_Planner\Actions\Content();
		$this->cached['actions__content_scan'] = new \Progress_Planner\Actions\Content_Scan();
		$this->cached['actions__maintenance']  = new \Progress_Planner\Actions\Maintenance();

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
	 * Magic method to get properties.
	 * We use this to avoid a lot of code duplication.
	 *
	 * Use a double underscore to separate namespaces:
	 * - get_foo() will return an instance of Progress_Planner\Foo.
	 * - get_foo_bar() will return an instance of Progress_Planner\Foo_Bar.
	 * - get_foo_bar__baz() will return an instance of Progress_Planner\Foo_Bar\Baz.
	 *
	 * @param string $name The name of the property.
	 * @param array  $arguments The arguments passed to the class constructor.
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		if ( 0 === strpos( $name, 'get_' ) ) {
			$cache_name = substr( $name, 4 );
		}

		if ( isset( $this->cached[ $cache_name ] ) ) {
			return $this->cached[ $cache_name ];
		}

		$class_name = implode( '\\', explode( '__', $cache_name ) );
		$class_name = 'Progress_Planner\\' . implode( '_', array_map( 'ucfirst', explode( '_', $class_name ) ) );
		if ( class_exists( $class_name ) ) {
			$this->cached[ $cache_name ] = new $class_name( $arguments );
			return $this->cached[ $cache_name ];
		}
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
