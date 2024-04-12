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
	const REMOTE_DOMAIN = 'https://progressplanner.com';

	/**
	 * The remote API endpoints namespace URL.
	 *
	 * @var string
	 */
	const REMOTE_API_URL = '/wp-json/progress-planner-saas/v1/';

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( Settings::get( 'license_key' ) ) {
			return;
		}

		// Redirect on plugin activation.
		\add_action( 'activated_plugin', [ $this, 'on_activate_plugin' ], 10 );

		// Handle saving data from the onboarding form response.
		\add_action( 'wp_ajax_progress_planner_save_onboard_data', [ $this, 'save_onboard_response' ] );
	}

	/**
	 * On plugin activation.
	 *
	 * @param string $plugin The plugin file.
	 *
	 * @return void
	 */
	public function on_activate_plugin( $plugin ) {
		if ( 'progress-planner/progress-planner.php' !== $plugin ) {
			return;
		}

		\wp_safe_redirect( \admin_url( 'admin.php?page=progress-planner' ) );
		exit;
	}

	/**
	 * The onboarding form.
	 *
	 * @return void
	 */
	public static function the_form() {
		$current_user = \wp_get_current_user();
		?>
		<form id="prpl-onboarding-form">
			<label>
				<?php \esc_html_e( 'First Name', 'progress-planner' ); ?>
				<input
					type="text"
					name="name"
					value="<?php echo \esc_attr( \get_user_meta( $current_user->ID, 'first_name', true ) ); ?>"
				>
			</label>
			<label>
				<?php \esc_html_e( 'Email', 'progress-planner' ); ?>
				<input
					type="email"
					name="email"
					value="<?php echo \esc_attr( $current_user->user_email ); ?>"
				>
			</label>
			<label>
				<input
					type="checkbox"
					name="consent"
					required
				>
				<?php \esc_html_e( 'I consent to sending my data to the remote server.', 'progress-planner' ); ?>
			</label>
			<input
				type="hidden"
				name="site"
				value="<?php echo \esc_attr( \site_url() ); ?>"
			>
			<input
				type="submit"
				value="<?php \esc_attr_e( 'Start the onboarding process!', 'progress-planner' ); ?>"
				class="button button-primary"
			>
		</form>

		<a style="display:none;" id="prpl-password-reset-link">
			<?php \esc_html_e( 'Registration successful. Set your password now and edit your profile', 'progress-planner' ); ?>
		</a>

		<div id="progress-planner-scan-progress" style="display:none;">
			<progress value="0" max="100"></progress>
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
		return self::REMOTE_DOMAIN . self::REMOTE_API_URL . 'get-nonce';
	}

	/**
	 * Get the onboarding remote URL.
	 *
	 * @return string
	 */
	public static function get_remote_url() {
		return self::REMOTE_DOMAIN . self::REMOTE_API_URL . 'onboard';
	}
}
