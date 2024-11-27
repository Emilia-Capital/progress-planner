<?php
/**
 * Badge object.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Badges;

/**
 * Badge class.
 */
abstract class Badge_Content extends Badge {

	/**
	 * Get the background color for the badge.
	 *
	 * @return string
	 */
	public function get_background() {
		return 'var(--prpl-background-blue)';
	}
}
