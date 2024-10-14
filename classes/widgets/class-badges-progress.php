<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * Badges progress widget.
 */
final class Badges_Progress extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'badges-progress';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
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
			PROGRESS_PLANNER_DIR . '/views/widgets/badges-progress.php',
			$this->id
		);
	}
}
