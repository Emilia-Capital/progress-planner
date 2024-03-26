<?php
/**
 * Abstract class for widgets.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

/**
 * Abstract class for widgets.
 */
abstract class Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->render();
	}

	/**
	 * Get the widget range.
	 *
	 * @return string
	 */
	public function get_range() {
		// phpcs:ignore WordPress.Security.NonceVerification
		return isset( $_GET['range'] )
			// phpcs:ignore WordPress.Security.NonceVerification
			? \sanitize_text_field( \wp_unslash( $_GET['range'] ) )
			: '-6 months';
	}

	/**
	 * Get the widget frequency.
	 *
	 * @return string
	 */
	public function get_frequency() {
		// phpcs:ignore WordPress.Security.NonceVerification
		return isset( $_GET['frequency'] )
			// phpcs:ignore WordPress.Security.NonceVerification
			? \sanitize_text_field( \wp_unslash( $_GET['frequency'] ) )
			: 'monthly';
	}

	/**
	 * Render the widget.
	 *
	 * @return void
	 */
	protected function render() {
		echo '<div class="prpl-widget-wrapper prpl-' . \esc_attr( $this->id ) . '">';
		$this->the_content();
		echo '</div>';
	}

	/**
	 * The widget content.
	 *
	 * @return void
	 */
	abstract protected function the_content();
}
