<?php
/**
 * Handle user badges.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Settings;

/**
 * Badges class.
 */
class Badges {

	/**
	 * Registered badges.
	 *
	 * @var array
	 */
	private static $badges = [];

	/**
	 * Register a badge.
	 *
	 * @param string $badge_id The badge ID.
	 * @param array  $args     The badge arguments.
	 *
	 * @return void
	 */
	public static function register_badge( $badge_id, $args ) {
		if ( ! isset( $args['id'] ) ) {
			$args['id'] = $badge_id;
		}
		self::$badges[ $badge_id ] = $args;
	}

	/**
	 * Get a badge by ID.
	 *
	 * @param string $badge_id The badge ID.
	 *
	 * @return array
	 */
	public static function get_badge( $badge_id ) {
		return isset( self::$badges[ $badge_id ] ) ? self::$badges[ $badge_id ] : [];
	}

	/**
	 * Get all badges.
	 *
	 * @return array
	 */
	public static function get_badges() {
		return self::$badges;
	}

	/**
	 * Get the progress for a badge.
	 *
	 * @param string $badge_id The badge ID.
	 *
	 * @return array
	 */
	public static function get_badge_progress( $badge_id ) {
		$badge = self::get_badge( $badge_id );
		if ( empty( $badge ) ) {
			return [];
		}

		if ( ! isset( $badge['steps'] ) ) {
			return $badge['progress_callback']();
		}

		return [];
	}

	/**
	 * Get the latest completed badge.
	 *
	 * @return string Returns the badge ID.
	 */
	public static function get_latest_completed_badge() {
		// Get the settings for badges.
		$settings = Settings::get( 'badges', [] );

		$latest_date = null;
		$latest_id   = null;

		// Loop badges to find the one that was completed last.
		foreach ( array_keys( self::$badges ) as $badge_id ) {
			$badge_progress = self::get_badge_progress( $badge_id );

			// Skip if the badge is not completed.
			if ( 100 > (int) $badge_progress['progress'] ) {
				continue;
			}

			// Set the first badge as the latest.
			if ( null === $latest_date ) {
				$latest_id = $badge_id;
				if ( isset( $settings[ $badge_id ]['date'] ) ) {
					$latest_date = $settings[ $badge_id ]['date'];
				}
				continue;
			}

			// Skip if the badge has no date.
			if ( ! isset( $settings[ $badge_id ]['date'] ) ) {
				continue;
			}

			// Compare dates.
			if ( \DateTime::createFromFormat( 'Y-m-d H:i:s', $settings[ $badge_id ]['date'] )->format( 'U' ) >= \DateTime::createFromFormat( 'Y-m-d H:i:s', $latest_date )->format( 'U' ) ) {
				$latest_date = $settings[ $badge_id ]['date'];
				$latest_id   = $badge_id;
			}
		}

		return $latest_id;
	}
}
