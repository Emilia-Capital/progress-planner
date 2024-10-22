<?php
/**
 * Badge object.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Badges\Badge;

use Progress_Planner\Base;
use Progress_Planner\Badges\Badge_Content;

/**
 * Badge class.
 */
final class Awesome_Author extends Badge_Content {

	/**
	 * The badge ID.
	 *
	 * @var string
	 */
	protected $id = 'awesome-author';

	/**
	 * The badge name.
	 *
	 * @return string
	 */
	public function get_name() {
		return \__( 'Awesome Author', 'progress-planner' );
	}

	/**
	 * Get the badge description.
	 *
	 * @return string
	 */
	public function get_description() {
		/* translators: %d: The number of new posts to write. */
		return sprintf( \esc_html__( 'Write %d new posts or pages', 'progress-planner' ), 50 );
	}

	/**
	 * Progress callback.
	 *
	 * @return array
	 */
	public function progress_callback() {
		global $progress_planner;
		$saved_progress = $this->get_saved();

		// If we have a saved value, return it.
		if ( isset( $saved_progress['progress'] ) && isset( $saved_progress['remaining'] ) ) {
			return $saved_progress;
		}

		// Get the number of new posts published.
		$new_count = count(
			$progress_planner->query->query_activities(
				[
					'category'   => 'content',
					'type'       => 'publish',
					'start_date' => $progress_planner->get_activation_date(),
				],
			)
		);

		$percent   = min( 100, floor( 100 * $new_count / 50 ) );
		$remaining = 50 - min( 50, $new_count );

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
