<?php
/**
 * A widget class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Widget;

/**
 * Badge_Streak class.
 */
final class Badge_Streak extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'badge-streak';

	/**
	 * Get the badge.
	 *
	 * @param string $context The context of the badges (content|maintenance|monthly).
	 *
	 * @return array
	 */
	public function get_details( $context ) {
		static $result = [];
		if ( isset( $result[ $context ] ) && ! empty( $result[ $context ] ) ) {
			return $result[ $context ];
		}

		$badges = \progress_planner()->get_badges()->get_badges( $context );

		// Get the badge to display.
		foreach ( $badges as $badge ) {
			$progress = $badge->get_progress();
			if ( 100 > $progress['progress'] ) {
				break;
			}
		}

		if ( ! isset( $badge ) || ! isset( $progress ) ) {
			return [];
		}

		$result[ $context ]['progress'] = $progress;
		$result[ $context ]['badge']    = $badge;

		$result[ $context ]['color'] = 'var(--prpl-color-accent-red)';
		if ( $result[ $context ]['progress']['progress'] > 50 ) {
			$result[ $context ]['color'] = 'var(--prpl-color-accent-orange)';
		}
		if ( $result[ $context ]['progress']['progress'] > 75 ) {
			$result[ $context ]['color'] = 'var(--prpl-color-accent-green)';
		}
		return $result[ $context ];
	}
}
