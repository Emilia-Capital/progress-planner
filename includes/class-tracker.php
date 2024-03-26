<?php
/**
 * GDPR-compliant Options tracker.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Collect and send the data to the remote server.
 */
class Tracker {

	/**
	 * The namespace for the tracker.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The interval (in seconds) to send the data.
	 *
	 * @var int
	 */
	protected $interval;

	/**
	 * The remote server URL.
	 *
	 * @var string
	 */
	protected $remote_server;

	/**
	 * The callback to collect the data to track.
	 *
	 * @var callable
	 */
	protected $collect_data_callback;

	/**
	 * Constructor.
	 *
	 * @param array $args The arguments.
	 *              [
	 *                  string   $args['namespace']     The namespace for the tracker.
	 *                                                  This is used to generate the transient name.
	 *                  int      $args['interval']      The interval (in seconds) to send the data.
	 *                  string   $args['remote-server'] The remote server URL.
	 *                  callable $args['collect-data']  The callback to collect the data to track.
	 *              ].
	 */
	public function __construct( $args = [] ) {
		$this->namespace             = $args['namespace'];
		$this->interval              = $args['interval'];
		$this->remote_server         = $args['remote-server'];
		$this->collect_data_callback = $args['collect-data'];
		$this->init();
	}

	/**
	 * Initialize the tracker.
	 */
	public function init() {
		add_action( 'init', [ $this, 'maybe_send_data' ] );
	}

	/**
	 * Maybe send the data to the remote server.
	 *
	 * Implement a weekly check to send the data.
	 */
	public function maybe_send_data() {
		$transient_name = $this->namespace . '_tracker_last_sent';
		$should_send    = ! get_transient( $transient_name );
		if ( $should_send ) {
			$this->send_data();
			set_transient( $transient_name, true, $this->interval );
		}
	}

	/**
	 * Send the data to the remote server.
	 *
	 * @return void
	 */
	public function send_data() {
		$callback          = $this->collect_data_callback;
		$remote_server_url = $this->remote_server . '?rest-route=/stats/v1/track';
		$response          = wp_remote_post(
			$remote_server_url,
			[
				'body'    => wp_json_encode( $callback() ),
				'headers' => [
					'Content-Type' => 'application/json',
				],
			]
		);
	}
}
