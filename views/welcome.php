<?php
/**
 * View for the welcome widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( false !== \get_option( 'progress_planner_license_key', false ) ) {
	return;
}

// Enqueue welcome styles.
\wp_enqueue_style(
	'progress-planner-welcome',
	PROGRESS_PLANNER_URL . '/assets/css/welcome.css',
	[],
	(string) filemtime( PROGRESS_PLANNER_DIR . '/assets/css/welcome.css' )
);

// Enqueue onboarding styles.
\wp_enqueue_style(
	'progress-planner-onboard',
	PROGRESS_PLANNER_URL . '/assets/css/onboard.css',
	[],
	(string) filemtime( PROGRESS_PLANNER_DIR . '/assets/css/onboard.css' )
);


?>
<div class="prpl-widget-wrapper prpl-welcome" popover="manual">
	<div class="welcome-header">
		<h1><?php \esc_html_e( 'Welcome to the Progress Planner plugin!', 'progress-planner' ); ?></h1>
		<span class="welcome-header-icon">
			<span class="slant"></span>
			<?php \progress_planner()->the_asset( 'images/icon_progress_planner.svg' ); ?>
		</span>
	</div>
	<div class="inner-content">
		<div class="left">
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
							value="<?php echo \esc_attr( \get_user_meta( \wp_get_current_user()->ID, 'first_name', true ) ); ?>"
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
							value="<?php echo \esc_attr( \wp_get_current_user()->user_email ); ?>"
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
				<br>
				<div class="prpl-form-notice">
					<label>
						<input
							type="checkbox"
							name="privacy-policy"
							class="prpl-input"
							required="required"
							value="1"
						>
						<?php
						printf(
						/* translators: %s: progressplanner.com/privacy-policy link */
							\esc_html__( 'By clicking the button below, you agree to the %s.', 'progress-planner' ),
							'<a href="https://progressplanner.com/privacy-policy/#h-plugin-privacy-policy" target="_blank">Privacy policy</a>'
						);
						?>
					</label>
				</div>
				<br>
				<div id="prpl-onboarding-submit-wrapper" style="display: none;">
					<div id="prpl-onboarding-submit-grid-wrapper">
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
				</div>
			</form>

			<div>
				<p id="prpl-account-created-message" style="display:none;">
					<?php
					printf(
						/* translators: %s: progressplanner.com link */
						\esc_html__( 'Success! We saved your data on %s so we can email you every week.', 'progress-planner' ),
						'<a href="https://prpl.fyi/home">ProgressPlanner.com</a>'
					);
					?>
				</p>
				<div id="progress-planner-scan-progress" style="display:none;">
					<progress value="0" max="100"></progress>
				</div>
			</div>
		</div>
		<div class="right">
			<img
				src="<?php echo \esc_url( PROGRESS_PLANNER_URL . '/assets/images/image_onboaring_block.png' ); ?>"
				alt=""
				class="onboarding"
			/>
		</div>
	</div>
</div>
<script>document.querySelector( '.prpl-widget-wrapper.prpl-welcome' ).showPopover();</script>
