<?php
/**
 * Popover for the subscribe form.
 *
 * @package Progress_Planner
 */

$prpl_current_user = \wp_get_current_user();
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
				value="<?php echo \esc_attr( \get_user_meta( $prpl_current_user->ID, 'first_name', true ) ); ?>"
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
				value="<?php echo \esc_attr( $prpl_current_user->user_email ); ?>"
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
