<?php
/**
 * Badge object.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Badges;

use ProgressPlanner\Badges\Badge;

/**
 * Badge class.
 */
abstract class Badge_Content extends Badge {

	/**
	 * The badge category.
	 *
	 * @var string
	 */
	protected $category = 'content_writing';
}
