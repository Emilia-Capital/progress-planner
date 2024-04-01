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
				name="site"
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
