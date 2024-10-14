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
	protected $endpoint = 'https://progressplanner.com/wp-json/progress-planner-saas/v1/share-badge-image?badge=';
}
