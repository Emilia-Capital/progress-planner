<?php
/**
 * Badges popover.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Popovers;

/**
 * Badges popover.
 */
final class Badge_Streak extends Popover {

	/**
	 * The popover ID.
	 *
	 * @var string
	 */
	protected $id = 'badge-streak';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		\progress_planner()->the_view( 'popovers/badge-streak.php' );
	}
}
