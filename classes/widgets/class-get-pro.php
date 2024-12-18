<?php
/**
 * A widget class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * Get_Pro class.
 */
final class Get_Pro extends \Progress_Planner\Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'get-pro';

	/**
	 * Render the widget.
	 *
	 * @return void
	 */
	public function render() {
		if ( \progress_planner()->is_pro_site() ) {
			return;
		}
		parent::render();
	}
}
