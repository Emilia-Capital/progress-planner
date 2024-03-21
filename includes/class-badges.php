<?php
/**
 * Handle user badges.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

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
	 * @return int
	 */
	public static function get_badge_progress( $badge_id ) {
		$badge = self::get_badge( $badge_id );
		if ( empty( $badge ) ) {
			return 0;
		}

		$progress = [];

		if ( ! isset( $badge['steps'] ) ) {
			return $badge['progress_callback']();
		}

		foreach ( $badge['steps'] as $step_id => $step ) {
			$progress[] = [
				'id'       => $step_id,
				'name'     => $step['name'],
				'icons'    => $step['icons-svg'],
				'progress' => $badge['progress_callback']( $step['target'] ),
			];
		}

		return $progress;
	}
}
