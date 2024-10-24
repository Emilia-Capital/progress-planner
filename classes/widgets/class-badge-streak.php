<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Badges;

/**
 * Badge content widget.
 */
final class Badge_Streak extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'badge-streak';

	/**
	 * Whether we should render the widget or not.
	 *
	 * @return bool
	 */
	protected function should_render() {
		$details = $this->get_badge_details();
		return ( 100 > (int) $details['progress']['progress'] );
	}

	/**
	 * Get the badge.
	 *
	 * @return array
	 */
	public function get_badge_details() {
		static $result = [];
		if ( ! empty( $result ) ) {
			return $result;
		}
		$badges = [
			'progress-padawan',
			'maintenance-maniac',
			'super-site-specialist',
		];

		// Get the badge to display.
		foreach ( $badges as $badge ) {
			$progress = Badges::get_badge_progress( $badge );
			if ( 100 > $progress['progress'] ) {
				break;
			}
		}
		$result['progress'] = $progress;
		$result['badge']    = Badges::get_badge( $badge );

		$result['color'] = 'var(--prpl-color-accent-red)';
		if ( $result['progress']['progress'] > 50 ) {
			$result['color'] = 'var(--prpl-color-accent-orange)';
		}
		if ( $result['progress']['progress'] > 75 ) {
			$result['color'] = 'var(--prpl-color-accent-green)';
		}
		return $result;
	}
}
