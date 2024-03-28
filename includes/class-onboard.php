<?php
/**
 * Onboarding class.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Settings;

/**
 * Onboarding class.
 */
class Onboard {

	/**
	 * The remote server URL.
	 *
	 * @var string
	 */
	const REMOTE_URL = 'http://ubuntu.orb.local';

	/**
	 * The onboarding form.
	 *
	 * @return void
	 */
	public static function the_form() {
		$current_user = wp_get_current_user();
		?>
		<form id="prpl-onboarding-form">
			<label>
				<?php esc_html_e( 'Email', 'progress-planner' ); ?>
				<input
					type="email"
					name="email"
					value="<?php echo esc_attr( $current_user->user_email ); ?>"
				>
			</label>
			<label>
				<?php esc_html_e( 'Name', 'progress-planner' ); ?>
				<input
					type="text"
					name="name"
					value="<?php echo esc_attr( $current_user->display_name ); ?>"
				>
			</label>
			<label>
				<input
					type="checkbox"
					name="consent"
					required
				>
				<?php esc_html_e( 'I consent to sending my data to the remote server.', 'progress-planner' ); ?>
			</label>

			<input
				type="hidden"
				name="remote_nonce"
				value="<?php echo esc_attr( self::get_remote_nonce() ); ?>"
			>
			<input
				type="hidden"
				name="site_token"
				value="<?php echo esc_attr( API::get_api_token() ); ?>"
			>
			<input
				type="hidden"
				name="site_url"
				value="<?php echo esc_attr( site_url() ); ?>"
			>
			<input
				type="submit"
				value="<?php esc_attr_e( 'Submit', 'progress-planner' ); ?>"
				class="button button-primary"
			>
		</form>
		<?php
	}

	/**
	 * Get a nonce from the remote server.
	 *
	 * @return string
	 */
	public static function get_remote_nonce() {
		$stored_nonce = \get_transient( 'progress_planner_remote_nonce' );
		if ( $stored_nonce ) {
			return $stored_nonce;
		}
		$response = wp_remote_get( self::REMOTE_URL . '/wp-json/progress-planner-saas/v1/get-nonce/site/' . md5( \site_url() ) );
		if ( is_wp_error( $response ) ) {
			return '';
		}
		$response_body = wp_remote_retrieve_body( $response );
		$data          = json_decode( $response_body, true );
		if ( ! is_array( $data ) || ! isset( $data['nonce'] ) ) {
			return '';
		}

		\set_transient( 'progress_planner_remote_nonce', $data['nonce'], HOUR_IN_SECONDS );

		return $data['nonce'];
	}
}
