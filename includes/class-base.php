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
use ProgressPlanner\Actions\Maintenance as Actions_Maintenance;
use ProgressPlanner\Settings;
use ProgressPlanner\Badges\Badge\Wonderful_Writer as Badge_Wonderful_Writer;
use ProgressPlanner\Badges\Badge\Awesome_Author as Badge_Awesome_Author;
use ProgressPlanner\Badges\Badge\Notorious_Novelist as Badge_Notorious_Novelist;
use ProgressPlanner\Badges\Badge\Progress_Professional as Badge_Progress_Professional;
use ProgressPlanner\Badges\Badge\Maintenance_Maniac as Badge_Maintenance_Maniac;
use ProgressPlanner\Badges\Badge\Super_Site_Specialist as Badge_Super_Site_Specialist;
use ProgressPlanner\API;

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
	 * The remote server ROOT URL.
	 *
	 * @var string
	 */
	const REMOTE_SERVER_ROOT_URL = 'https://joost.blog';

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

		// TODO: DELETE THIS LINE.
		$this->set_points_config();
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

		// Content badges.
		new Badge_Wonderful_Writer();
		new Badge_Awesome_Author();
		new Badge_Notorious_Novelist();

		// Maintenance badges.
		new Badge_Progress_Professional();
		new Badge_Maintenance_Maniac();
		new Badge_Super_Site_Specialist();

		new API();
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

	/**
	 * Get the feed from the blog.
	 *
	 * @return array
	 */
	public function get_blog_feed() {
		$feed = \get_transient( 'prpl_blog_feed' );
		if ( false === $feed ) {
			// Get the feed using the REST API.
			$response = \wp_remote_get( self::REMOTE_SERVER_ROOT_URL . '/wp-json/wp/v2/posts/?per_page=3' );
			if ( \is_wp_error( $response ) ) {
				return [];
			}
			$feed = json_decode( \wp_remote_retrieve_body( $response ), true );
			\set_transient( 'prpl_blog_feed', $feed, 1 * DAY_IN_SECONDS );
		}
		return $feed;
	}

	/**
	 * THIS SHOULD BE DELETED.
	 * WE ONLY HAVE IT HERE TO EXPERIMENT WITH THE NUMBERS
	 * WE'LL HAVE TO USE FOR STATS/SCORES.
	 *
	 * TODO: DELETE THIS METHOD.
	 *
	 * @return mixed
	 */
	public function set_points_config() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['content-publish'] ) ) {
			self::$points_config['content']['publish'] = (float) $_GET['content-publish'];
		}

		if ( isset( $_GET['content-update'] ) ) {
			self::$points_config['content']['update'] = (float) $_GET['content-update'];
		}

		if ( isset( $_GET['content-delete'] ) ) {
			self::$points_config['content']['delete'] = (float) $_GET['content-delete'];
		}

		if ( isset( $_GET['content-word-multiplier-100'] ) ) {
			self::$points_config['content']['word-multipliers'][100] = (float) $_GET['content-word-multiplier-100'];
		}

		if ( isset( $_GET['content-word-multiplier-350'] ) ) {
			self::$points_config['content']['word-multipliers'][350] = (float) $_GET['content-word-multiplier-350'];
		}

		if ( isset( $_GET['content-word-multiplier-1000'] ) ) {
			self::$points_config['content']['word-multipliers'][1000] = (float) $_GET['content-word-multiplier-1000'];
		}

		if ( isset( $_GET['score-target'] ) ) {
			self::$points_config['score_target'] = (float) $_GET['score-target'];
		}

		if ( isset( $_GET['maintenance'] ) ) {
			self::$points_config['maintenance'] = (float) $_GET['maintenance'];
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
}
