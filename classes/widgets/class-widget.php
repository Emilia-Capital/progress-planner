<?php
/**
 * Abstract class for widgets.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * Widgets class.
 *
 * All widgets should extend this class.
 */
abstract class Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id;

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
	public function render() {
		if ( ! $this->should_render() ) {
			return;
		}

		$stylesheet = "/assets/css/page-widgets/{$this->id}.css";
		if ( \file_exists( PROGRESS_PLANNER_DIR . $stylesheet ) ) {
			\wp_enqueue_style(
				'prpl-widget-' . $this->id,
				PROGRESS_PLANNER_URL . $stylesheet,
				[],
				(string) filemtime( PROGRESS_PLANNER_DIR . $stylesheet )
			);
		}
		$classes = [
			'prpl-widget-wrapper',
			'prpl-' . \esc_attr( $this->id ),
		];
		echo '<div class="' . esc_attr( \implode( ' ', $classes ) ) . '">';
		echo '<div class="widget-inner-container">';
		$this->the_content();
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Whether we should render the widget or not.
	 *
	 * @return bool
	 */
	protected function should_render() {
		return true;
	}

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	public function the_content() {
		/**
		 * Filters the template to use for the widget.
		 *
		 * @param string $template The template to use.
		 * @param string $id       The widget ID.
		 *
		 * @return string The template to use.
		 */
		include \apply_filters( // phpcs:ignore PEAR.Files.IncludingFile.UseRequire
			'progress_planner_widgets_template',
			PROGRESS_PLANNER_DIR . "/views/page-widgets/{$this->id}.php",
			$this->id
		);
	}
}
