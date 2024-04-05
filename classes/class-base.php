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
use ProgressPlanner\Actions\Content as Actions_Content;
use ProgressPlanner\Actions\Content_Scan as Actions_Content_Scan;
use ProgressPlanner\Actions\Maintenance as Actions_Maintenance;
use ProgressPlanner\Settings;
use ProgressPlanner\Badges\Badge\Wonderful_Writer as Badge_Wonderful_Writer;
use ProgressPlanner\Badges\Badge\Awesome_Author as Badge_Awesome_Author;
use ProgressPlanner\Badges\Badge\Notorious_Novelist as Badge_Notorious_Novelist;
use ProgressPlanner\Badges\Badge\Progress_Professional as Badge_Progress_Professional;
use ProgressPlanner\Badges\Badge\Maintenance_Maniac as Badge_Maintenance_Maniac;
use ProgressPlanner\Badges\Badge\Super_Site_Specialist as Badge_Super_Site_Specialist;
use ProgressPlanner\Rest_API;

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
	 * An array of configuration values for points awarded by action-type.
	 *
	 * @var array
	 */
	public static $points_config = [
		'content'      => [
			'publish'          => 50,
			'update'           => 10,
			'delete'           => 5,
			'word-multipliers' => [
				100  => 1.1,
				350  => 1.25,
				1000 => 0.8,
			],
		],
		'score-target' => 200,
		'maintenance'  => 10,
	];

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
		$this->init();
	}

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		// Basic classes.
		new Admin_Page();
		new Admin_Dashboard_Widget();
		new Actions_Content();
		new Actions_Maintenance();
		new Actions_Content_Scan();

		// Content badges.
		new Badge_Wonderful_Writer();
		new Badge_Awesome_Author();
		new Badge_Notorious_Novelist();

		// Maintenance badges.
		new Badge_Progress_Professional();
		new Badge_Maintenance_Maniac();
		new Badge_Super_Site_Specialist();

		// REST API.
		new Rest_API();

		// Onboarding.
		new Onboard();
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
	 * Get the activation date.
	 *
	 * @return \DateTime
	 */
	public static function get_activation_date() {
		$activation_date = Settings::get( 'activation_date' );
		if ( ! $activation_date ) {
			$activation_date = new \DateTime();
			Settings::set( 'activation_date', $activation_date->format( 'Y-m-d' ) );
			return $activation_date;
		}
		return \DateTime::createFromFormat( 'Y-m-d', $activation_date );
	}
}
