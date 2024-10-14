<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * Latest badge widget.
 */
final class Latest_Badge extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'latest-badge';

	/**
	 * The endpoint to get the badge image.
	 *
	 * @var string
	 */
	const ENDPOINT = 'https://progressplanner.com/wp-json/progress-planner-saas/v1/share-badge-image?badge=';

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
			PROGRESS_PLANNER_DIR . '/views/widgets/latest-badge.php',
			$this->id
		);
	}
}
