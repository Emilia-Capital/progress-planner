<?php
/**
 * Subscribe form popup.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Popovers;

/**
 * Subscribe form popup.
 */
final class Subscribe_Form extends Popover {

	/**
	 * The popover ID.
	 *
	 * @var string
	 */
	protected $id = 'subscribe-form';

	/**
	 * Render the triggering button.
	 *
	 * @return void
	 */
	public function render_button() {

		$saved_license_key = \get_option( 'progress_planner_license_key', 'no-license' );
		if ( false !== $saved_license_key && 'no-license' !== $saved_license_key ) {
			return;
		}
		?>
		<!-- The triggering button. -->
		<button class="prpl-info-icon" popovertarget="prpl-popover-<?php echo \esc_attr( $this->id ); ?>" id="prpl-popover-subscribe-form-trigger">
			<span class="dashicons dashicons-email-alt"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'Subscribe', 'progress-planner' ); ?>
		</button>
		<?php
	}

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		$current_user = \wp_get_current_user();
		?>

		<h2><?php \esc_html_e( 'Subscribe to weekly emails', 'progress-planner' ); ?></h2>

		<form id="prpl-settings-license-form">

			<p>
			<?php
			printf(
				/* translators: %s: progressplanner.com link */
				\esc_html__( 'We can send you weekly emails with your own to-do’s, your activity stats and nudges to keep you working on your site. To do this, we’ll create an account for you on %s.', 'progress-planner' ),
				'<a href="https://prpl.fyi/home" target="_blank">progressplanner.com</a>'
			)
			?>
			</p>
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
			<button id="submit-license-key" class="button button-primary"><?php \esc_html_e( 'Subscribe', 'progress-planner' ); ?></button>
		</form>

		<?php
	}
}
