<?php
/**
 * A widget class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * Latest_Badge class.
 */
final class Latest_Badge extends \Progress_Planner\Widget {

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
	public $endpoint = 'https://progressplanner.com/wp-json/progress-planner-saas/v1/share-badge-image?badge=';
}
