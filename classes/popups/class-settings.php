<?php
/**
 * Settings popup.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Popups;

use Progress_Planner\Activities\Content_Helpers;

/**
 * Settings popup.
 */
final class Settings extends Popup {

	/**
	 * The popover ID.
	 *
	 * @var string
	 */
	protected $id = 'settings';

	/**
	 * Render the triggering button.
	 *
	 * @return void
	 */
	public function render_button() {
		?>
		<!-- The triggering button. -->
		<button class="prpl-info-icon" popovertarget="prpl-popover-<?php echo \esc_attr( $this->id ); ?>" id="prpl-popover-settings-trigger">
			<span class="dashicons dashicons-admin-generic"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'Settings', 'progress-planner' ); ?>
		</button>
		<?php
	}

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		$saved_settings = Content_Helpers::get_post_types_names();
		$post_types     = \array_filter( \get_post_types( [ 'public' => true ] ), 'is_post_type_viewable' );
		unset( $post_types['attachment'] );
		unset( $post_types['elementor_library'] ); // Elementor templates are not a post type we want to track.
		?>

		<h2><?php \esc_html_e( 'Settings', 'progress-planner' ); ?></h2>

		<form id="prpl-settings-form">
			<h3><?php \esc_html_e( 'Post Types', 'progress-planner' ); ?></h3>
			<p><?php \esc_html_e( 'Select the post types you want to include in activity scores. This setting will affect which post-type activities get tracked.', 'progress-planner' ); ?></p>
			<div id="prpl-settings-post-types-include">
				<?php foreach ( $post_types as $post_type ) : ?>
					<label>
						<input
							type="checkbox"
							name="prpl-settings-post-types-include[]"
							value="<?php echo \esc_attr( $post_type ); ?>"
							<?php checked( \in_array( $post_type, $saved_settings, true ) ); ?>
						/>
						<?php echo \esc_html( \get_post_type_object( $post_type )->labels->name ); ?>
					</label>
				<?php endforeach; ?>
			</div>

			<button id="submit-include-post-types" class="button button-primary"><?php \esc_html_e( 'Save', 'progress-planner' ); ?></button>
		</form>

		<?php
		$saved_license_key = \get_option( 'progress_planner_license_key', 'no-license' );

		if ( false === $saved_license_key || 'no-license' === $saved_license_key ) :
			?>
		<form id="prpl-settings-license-form">

			<?php
			$current_user = \wp_get_current_user();
			?>
			<h3><?php \esc_html_e( 'Subscribe to weekly emails', 'progress-planner' ); ?></h3>
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
		<?php endif; ?>

		<?php
	}
}
