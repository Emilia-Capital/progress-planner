<?php
/**
 * Activity Scores Widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Popups;

/**
 * Activity Scores Widget.
 */
abstract class Popup {

	/**
	 * The popup ID.
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
	 * Render the widget content.
	 */
	public function render() {
		?>
		<div id="prpl-popup-<?php echo \esc_attr( $this->id ); ?>" class="prpl-popup">
			<?php $this->the_content(); ?>
		</div>
		<?php
	}

	/**
	 * Render the widget content.
	 */
	abstract protected function the_content();
}
