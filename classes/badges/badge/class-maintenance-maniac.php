<?php
/**
 * Badge object.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Badges\Badge;

use Progress_Planner\Badges\Badge_Maintenance;


/**
 * Badge class.
 */
final class Maintenance_Maniac extends Badge_Maintenance {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id = 'maintenance-maniac';

	/**
	 * The badge name.
	 *
	 * @return string
	 */
	public function get_name() {
		return \__( 'Maintenance Maniac', 'progress-planner' );
	}

	/**
	 * Get the badge description.
	 *
	 * @return string
	 */
	public function get_description() {
		/* translators: %d: The number of weeks. */
		return sprintf( \esc_html__( '%d weeks streak', 'progress-planner' ), 26 );
	}

	/**
	 * The badge icons.
	 *
	 * @return array
	 */
	public function get_icons_svg() {
		return [
			'pending'  => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/maintenance-maniac-bw.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/maintenance-maniac-bw.svg',
			],
			'complete' => [
				'path' => \PROGRESS_PLANNER_DIR . '/assets/images/badges/maintenance-maniac.svg',
				'url'  => \PROGRESS_PLANNER_URL . '/assets/images/badges/maintenance-maniac.svg',
			],
		];
	}

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	public function progress_callback() {
		$saved_progress = $this->get_saved();

		// If we have a saved value, return it.
		if ( isset( $saved_progress['progress'] ) && isset( $saved_progress['remaining'] ) ) {
			return $saved_progress;
		}

		$max_streak = $this->get_goal()->get_streak()['max_streak'];
		$percent    = min( 100, floor( 100 * $max_streak / 26 ) );
		$remaining  = 26 - min( 26, $max_streak );

		$this->save_progress(
			[
				'progress'  => $percent,
				'remaining' => $remaining,
			]
		);

		return [
			'progress'  => $percent,
			'remaining' => $remaining,
		];
	}
}
