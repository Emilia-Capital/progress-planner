<?php
/**
 * Handle communication with the progress-planner platform API.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Class Platform
 */
class Platform {
	/**
	 * The API endpoint.
	 *
	 * @var string
	 */
	const API_ENDPOINT = 'http://ubuntu.orb.local/wp-json/progress-planner/v1/ping';

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize the class.
	 */
	public function init() {
		$this->request( $this->get_data() );
	}

	/**
	 * Get the data to send.
	 *
	 * @return array
	 */
	private function get_data() {
		return [
			'site_url' => \get_site_url(),
			'api_key'  => $this->get_api_key(),
			'data'     => 'test data',
		];
	}

	/**
	 * Get the request headers.
	 *
	 * @return array
	 */
	private function get_headers() {
		return [
			'Authorization' => 'Bearer ' . $this->get_api_key(),
		];
	}

	/**
	 * Get the API key.
	 *
	 * @return string
	 */
	private function get_api_key() {
		return '123';
	}

	/**
	 * Send a request to the API.
	 *
	 * @return array
	 */
	public function request() {
		$args = [
			'method'  => 'POST',
			'headers' => $this->get_headers(),
			'body'    => $this->get_data(),
		];

		$response = \wp_remote_post( self::API_ENDPOINT, $args );

		return \json_decode( \wp_remote_retrieve_body( $response ), true );
	}
}
