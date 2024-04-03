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
	const REMOTE_URL = 'https://progressplanner.com';

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'wp_ajax_progress_planner_save_onboard_data', [ $this, 'save_onboard_response' ] );
	}

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
				<input
					type="checkbox"
					name="consent"
					required
				>
				<?php esc_html_e( 'I consent to sending my data to the remote server.', 'progress-planner' ); ?>
			</label>
			<input
				type="hidden"
				name="site"
				value="<?php echo esc_attr( site_url() ); ?>"
			>
			<input
				type="submit"
				value="<?php esc_attr_e( 'Start the onboarding process!', 'progress-planner' ); ?>"
				class="button button-primary"
			>
		</form>
		<div id="progress-planner-onboard-responses">
			<a style="display:none;" id="prpl-password-reset-link">
				<?php esc_html_e( 'Registration successful. Set your password now and edit your profile', 'progress-planner' ); ?>
			</a>
			<ul class="onboard-steps">
				<li class="registering-site">
					<?php esc_html_e( 'Registering your site...', 'progress-planner' ); ?>
				</li>
				<li class="scanning-posts">
					<?php esc_html_e( 'Scanning your posts...', 'progress-planner' ); ?>
				</li>
			</ul>
			<div id="progress-planner-scan-progress" style="display:none;">
				<progress value="0" max="100"></progress>
			</div>
		</div>
		<?php
	}

	/**
	 * Save the onboarding response.
	 *
	 * @return void
	 */
	public function save_onboard_response() {
		// Check the nonce.
		if ( ! \check_ajax_referer( 'progress_planner', 'nonce', false ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Invalid nonce.', 'progress-planner' ) ] );
		}

		if ( ! isset( $_POST['key'] ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Missing data.', 'progress-planner' ) ] );
		}

		$license_key = \sanitize_text_field( wp_unslash( $_POST['key'] ) );

		Settings::set( 'license_key', $license_key );
		\wp_send_json_success(
			[
				'message' => \esc_html__( 'Onboarding data saved.', 'progress-planner' ),
			]
		);
	}

	/**
	 * Get the remote nonce URL.
	 *
	 * @return string
	 */
	public static function get_remote_nonce_url() {
		return self::REMOTE_URL . '/wp-json/progress-planner-saas/v1/get-nonce';
	}

	/**
	 * Get the onboarding remote URL.
	 *
	 * @return string
	 */
	public static function get_remote_url() {
		return self::REMOTE_URL . '/wp-json/progress-planner-saas/v1/onboard';
	}
}
