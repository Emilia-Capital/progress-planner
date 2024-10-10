<?php
/**
 * Abstract class for widgets.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

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
	 * The col-span for the grid layout.
	 *
	 * @var int
	 */
	protected $colspan = 2;

	/**
	 * The row-span for the grid layout.
	 *
	 * @var int
	 */
	protected $rowspan = 2;

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
		if ( ! $this->should_render() ) {
			return;
		}
		$classes = [
			'prpl-widget-wrapper',
			'prpl-' . \esc_attr( $this->id ),
			'prpl-widget-colspan-' . \esc_attr( $this->colspan ),
			'prpl-widget-rowspan-' . \esc_attr( $this->rowspan ),
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
	 * The widget content.
	 *
	 * @return void
	 */
	abstract protected function the_content();
}
