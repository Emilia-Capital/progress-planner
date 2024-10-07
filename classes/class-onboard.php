<?php
/**
 * Onboarding class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

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
		if ( \get_option( 'progress_planner_license_key' ) ) {
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
			<div class="prpl-form-notice">
				<?php
				printf(
					/* translators: %s: progressplanner.com link */
					\esc_html__( 'We can send you weekly emails with your own to-do’s, your activity stats and nudges to keep you working on your site. To do this, we’ll create an account for you on %s.', 'progress-planner' ),
					'<a href="https://prpl.fyi/home" target="_blank">progressplanner.com</a>'
				)
				?>
			</div>
			<br>
			<div class="prpl-onboard-form-radio-select">
				<label>
					<input type="radio" name="with-email" value="yes" checked>
					<span class="prpl-label-content">
						<?php \esc_html_e( 'Yes, send me weekly emails!', 'progress-planner' ); ?>
					</span>
				</label>
				<label>
					<input type="radio" name="with-email" value="no">
					<span class="prpl-label-content">
						<?php \esc_html_e( 'Please do not email me.', 'progress-planner' ); ?>
					</span>
				</label>
			</div>
			<br>
			<div class="prpl-form-fields">
				<label>
					<span class="prpl-label-content">
						<?php \esc_html_e( 'First name', 'progress-planner' ); ?>
					</span>
					<input
						type="text"
						name="name"
						class="prpl-input"
						required
						value="<?php echo \esc_attr( \get_user_meta( $current_user->ID, 'first_name', true ) ); ?>"
					>
				</label>
				<label>
					<span class="prpl-label-content">
						<?php \esc_html_e( 'Email', 'progress-planner' ); ?>
					</span>
					<input
						type="email"
						name="email"
						class="prpl-input"
						required
						value="<?php echo \esc_attr( $current_user->user_email ); ?>"
					>
				</label>
				<input
					type="hidden"
					name="site"
					value="<?php echo \esc_attr( \set_url_scheme( \site_url() ) ); ?>"
				>
				<input
					type="hidden"
					name="timezone_offset"
					value="<?php echo (float) ( \wp_timezone()->getOffset( new \DateTime( 'midnight' ) ) / 3600 ); ?>"
				>
			</div>
			<div id="prpl-onboarding-submit-grid-wrapper">
				<span></span><!-- Empty span for styling (grid layout). -->
				<span>
					<input
						type="submit"
						value="<?php \esc_attr_e( 'Get going and send me weekly emails', 'progress-planner' ); ?>"
						class="prpl-button-primary"
					>
				</span>
			</div>
			<input
				type="submit"
				value="<?php \esc_attr_e( 'Continue without emailing me', 'progress-planner' ); ?>"
				class="prpl-button-secondary prpl-button-secondary--no-email prpl-hidden"
			>
		</form>

		<div>
			<p id="prpl-account-created-message" style="display:none;">
				<?php
				// translators: %s: progressplanner.com link.
				printf( \esc_html__( 'Success! We saved your data on %s so we can email you every week.', 'progress-planner' ), '<a href="https://prpl.fyi/home">ProgressPlanner.com</a>' );
				?>
			</p>
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

		if ( \update_option( 'progress_planner_license_key', $license_key, false ) ) {
			\wp_send_json_success(
				[
					'message' => \esc_html__( 'Onboarding data saved.', 'progress-planner' ),
				]
			);
		}
		\wp_send_json_error( [ 'message' => \esc_html__( 'Unable to save data.', 'progress-planner' ) ] );
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
