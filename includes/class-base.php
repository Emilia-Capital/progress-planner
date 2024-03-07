<?php
/**
 * Progress Planner main plugin class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Query;
use ProgressPlanner\Admin\Page as Admin_page;
use ProgressPlanner\Admin\Dashboard_Widget as Admin_Dashboard_Widget;
use ProgressPlanner\Scan\Content as Scan_Content;
use ProgressPlanner\Scan\Maintenance as Scan_Maintenance;

/**
 * Main plugin class.
 */
class Base {

	/**
	 * An instance of this class.
	 *
	 * @var \ProgressPlanner\Base
	 */
	private static $instance;

	/**
	 * Get the single instance of this class.
	 *
	 * @return \ProgressPlanner\Base
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		new Admin_Page();
		new Admin_Dashboard_Widget();
		new Scan_Content();
		new Scan_Maintenance();
	}

	/**
	 * Get the query object.
	 *
	 * @return \ProgressPlanner\Query
	 */
	public function get_query() {
		return Query::get_instance();
	}

	/**
	 * Get the badges object.
	 *
	 * @return \ProgressPlanner\Badges
	 */
	public function get_badges() {
		return new Badges();
	}

	/**
	 * Get the activation date.
	 *
	 * @return \DateTime
	 */
	public static function get_activation_date() {
		$activation_date = get_option( 'progress_planner_activation_date' );
		if ( ! $activation_date ) {
			$activation_date = new \DateTime();
			update_option( 'progress_planner_activation_date', $activation_date->format( 'Y-m-d' ) );
			return $activation_date;
		}
		return \DateTime::createFromFormat( 'Y-m-d', $activation_date );
	}

	/**
	 * THIS SHOULD BE DELETED.
	 * WE ONLY HAVE IT HERE TO EXPERIMENT WITH THE NUMBERS
	 * WE'LL HAVE TO USE FOR STATS/SCORES.
	 *
	 * TODO: DELETE THIS METHOD.
	 *
	 * @param string $param The parameter to get. Null to get all.
	 *
	 * @return mixed
	 */
	public function get_dev_config( $param = null ) {
		$config = [
			'content-publish'                    => 50,
			'content-update'                     => 10,
			'content-delete'                     => 5,
			'content-100-minus-words-multiplier' => 0.8,
			'content-100-plus-words-multiplier'  => 1.1,
			'content-350-plus-words-multiplier'  => 1.25,
			'content-1000-plus-words-multiplier' => 0.8,
			'maintenance'                        => 10,
			'activity-score-target'              => 200,
		];

		// phpcs:disable WordPress.Security
		foreach ( $config as $key => $value ) {
			$config[ $key ] = isset( $_GET[ $key ] ) ? (float) $_GET[ $key ] : $value;
		}
		// phpcs:enable WordPress.Security

		return null === $param ? $config : $config[ $param ];
	}
}
