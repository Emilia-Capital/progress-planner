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
	 * The col-span for the 12-column grid layout.
	 *
	 * @var int
	 */
	protected $colspan = 4;

	/**
	 * The row-span for the grid layout.
	 *
	 * @var int
	 */
	protected $rowspan = 6;

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
		$classes = [
			'prpl-widget-wrapper',
			'prpl-' . \esc_attr( $this->id ),
			'prpl-widget-colspan-' . \esc_attr( (string) $this->colspan ),
			'prpl-widget-rowspan-' . \esc_attr( (string) $this->rowspan ),
		];
		echo '<div class="' . esc_attr( \implode( ' ', $classes ) ) . '">';
		$this->the_content();
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
	 * Render a big counter.
	 *
	 * @param int    $number The number to display.
	 * @param string $text   The text to display.
	 *
	 * @return void
	 */
	protected function render_big_counter( int $number, $text ) {
		?>
		<div class="counter-big-wrapper">
			<span class="counter-big-number">
				<?php echo \esc_html( \number_format_i18n( $number ) ); ?>
			</span>
			<span class="counter-big-text">
				<?php echo \esc_html( $text ); ?>
			</span>
		</div>
		<?php
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
		include \apply_filters(
			'progress_planner_widgets_template',
			PROGRESS_PLANNER_DIR . "/views/widgets/{$this->id}.php",
			$this->id
		);
	}
}
