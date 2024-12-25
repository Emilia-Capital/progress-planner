<?php
/**
 * Class Test_API_Get_Stats
 *
 * @package Progress_Planner\Tests
 */

namespace Progress_Planner\Tests;

use WP_UnitTestCase;
use WP_REST_Server;
use WP_REST_Request;

/**
 * Test_API_Get_Stats test case.
 */
class Test_API_Get_Stats extends \WP_UnitTestCase {

	/**
	 * Holds the WP REST Server object.
	 *
	 * @var WP_REST_Server
	 */
	private $server;

	/**
	 * The token for the test.
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Create a item for our test and initiate REST API.
	 */
	public function setUp(): void {
		parent::setUp();

		$this->token = '123456789';

		// Add a fake license key.
		update_option( 'progress_planner_license_key', $this->token );

		// Initiating the REST API.
		global $wp_rest_server;
		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;
		do_action( 'rest_api_init' );
	}

	/**
	 * Delete the item after the test.
	 */
	public function tearDown(): void {
		parent::tearDown();

		// Delete the fake license key.
		delete_option( 'progress_planner_license_key' );

		global $wp_rest_server;
		$wp_rest_server = null;
	}

	/**
	 * Test the endpoint for Person CPT.
	 *
	 * @return void.
	 */
	public function testEndpoint() {

		$request  = new WP_REST_Request( 'GET', '/progress-planner/v1/get-stats/' . $this->token );
		$response = $this->server->dispatch( $request );

		// Check if the response is successful.
		$this->assertEquals( 200, $response->get_status() );

		// Get the data.
		$data = $response->get_data();

		// Check if the data has the expected keys.
		$data_to_check = [
			'pending_updates',
			'weekly_posts',
			'activities',
			'website_activity',
			'badges',
			'latest_badge',
			'scores',
			'website',
			'timezone_offset',
			'todo',
			'plugin_url',
		];

		foreach ( $data_to_check as $key ) {
			$this->assertArrayHasKey( $key, $data );
		}
	}
}
