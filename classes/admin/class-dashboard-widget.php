<?php
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

/**
 * Class Dashboard_Widget
 */
abstract class Dashboard_Widget {

	/**
	 * The widghet ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
	}

	/**
	 * Add the dashboard widget.
	 *
	 * @return void
	 */
	public function add_dashboard_widget() {
		\wp_add_dashboard_widget(
			$this->id,
			$this->get_title(),
			[ $this, 'render_widget' ]
		);
	}

	/**
	 * Get the title of the widget.
	 *
	 * @return string
	 */
	abstract protected function get_title();

	/**
	 * Render the dashboard widget.
	 *
	 * @return void
	 */
	abstract public function render_widget();
}
