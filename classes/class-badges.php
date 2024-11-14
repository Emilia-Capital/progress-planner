<?php
/**
 * Handle user badges.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

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
			\progress_planner()->get_badges__content__wonderful_writer(),
			\progress_planner()->get_badges__content__bold_blogger(),
			\progress_planner()->get_badges__content__awesome_author(),
		];

		$this->maintenance = [
			\progress_planner()->get_badges__maintenance__progress_padawan(),
			\progress_planner()->get_badges__maintenance__maintenance_maniac(),
			\progress_planner()->get_badges__maintenance__super_site_specialist(),
		];

		$this->monthly = \Progress_Planner\Badges\Monthly::get_instances();
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
	 * Get a single badge.
	 *
	 * @param string $badge_id The badge ID.
	 *
	 * @return \Progress_Planner\Badges\Badge|null
	 */
	public function get_badge( $badge_id ) {
		foreach ( [ 'content', 'maintenance', 'monthly' ] as $context ) {
			foreach ( $this->$context as $badge ) {
				if ( $badge->get_id() === $badge_id ) {
					return $badge;
				}
			}
		}
		return null;
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

		foreach ( [ 'content', 'maintenance', 'monthly' ] as $context ) {
			foreach ( $this->$context as $badge ) {
				// Skip if the badge has no date.
				if ( ! isset( $settings[ $badge->get_id() ]['date'] ) ) {
					continue;
				}

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
