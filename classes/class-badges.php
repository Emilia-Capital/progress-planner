<?php
/**
 * Handle user badges.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Badges\Content\Wonderful_Writer;
use Progress_Planner\Badges\Content\Bold_Blogger;
use Progress_Planner\Badges\Content\Awesome_Author;
use Progress_Planner\Badges\Maintenance\Progress_Padawan;
use Progress_Planner\Badges\Maintenance\Maintenance_Maniac;
use Progress_Planner\Badges\Maintenance\Super_Site_Specialist;
use Progress_Planner\Badges\Monthly;
/**
 * Badges class.
 */
class Badges {

	/**
	 * Content badges.
	 *
	 * @var array<\Progress_Planner\Badges\Badge>
	 */
	private $content = [];

	/**
	 * Maintenance badges.
	 *
	 * @var array<\Progress_Planner\Badges\Badge>
	 */
	private $maintenance = [];

	/**
	 * Monthly badges.
	 *
	 * @var array<\Progress_Planner\Badges\Badge>
	 */
	private $monthly = [];

	/**
	 * Latest completed badge.
	 *
	 * @var \Progress_Planner\Badges\Badge|null
	 */
	private $latest_completed_badge;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->content = [
			new Wonderful_Writer(),
			new Bold_Blogger(),
			new Awesome_Author(),
		];

		$this->maintenance = [
			new Progress_Padawan(),
			new Maintenance_Maniac(),
			new Super_Site_Specialist(),
		];

		$this->monthly = Monthly::get_instances();
	}

	/**
	 * Get the badges for a context.
	 *
	 * @param string $context The badges context (content|maintenance|monthly).
	 *
	 * @return array<\Progress_Planner\Badges\Badge>
	 */
	public function get_badges( $context ) {
		if ( ! isset( $this->$context ) ) {
			return [];
		}

		return $this->$context;
	}

	/**
	 * Get the latest completed badge.
	 *
	 * @return \Progress_Planner\Badges\Badge|null
	 */
	public function get_latest_completed_badge() {
		if ( $this->latest_completed_badge ) {
			return $this->latest_completed_badge;
		}

		// Get the settings for badges.
		$settings = \progress_planner()->get_settings()->get( 'badges', [] );

		$latest_date = null;

		$flat_badges = array_merge(
			$this->content,
			$this->maintenance,
			$this->monthly,
		);

		foreach ( [ 'content', 'maintenance', 'monthly' ] as $context ) {
			foreach ( $this->$context as $badge ) {
				$badge_progress = $badge->get_progress();

				// Continue if the badge is not completed.
				if ( 100 > (int) $badge_progress['progress'] ) {
					continue;
				}

				// Set the first badge as the latest.
				if ( null === $latest_date ) {
					$this->latest_completed_badge = $badge;
					if ( isset( $settings[ $badge->get_id() ]['date'] ) ) {
						$latest_date = $settings[ $badge->get_id() ]['date'];
					}
					continue;
				}

				// Skip if the badge has no date.
				if ( ! isset( $settings[ $badge->get_id() ]['date'] ) ) {
					continue;
				}

				// Compare dates.
				if ( \DateTime::createFromFormat( 'Y-m-d H:i:s', $settings[ $badge->get_id() ]['date'] )->format( 'U' ) >= \DateTime::createFromFormat( 'Y-m-d H:i:s', $latest_date )->format( 'U' ) ) {
					$latest_date                  = $settings[ $badge->get_id() ]['date'];
					$this->latest_completed_badge = $badge;
				}
			}
		}

		return $this->latest_completed_badge;
	}
}
