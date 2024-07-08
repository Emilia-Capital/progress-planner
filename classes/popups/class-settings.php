<?php
/**
 * Settings popup.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Popups;

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
		<button class="prpl-info-icon" popovertarget="prpl-popover-<?php echo \esc_attr( $this->id ); ?>">
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
		$saved_settings = \Progress_Planner\Settings::get( 'exclude_post_types', [] );
		$post_types     = \array_filter( \get_post_types( [ 'public' => true ] ), 'is_post_type_viewable' );
		unset( $post_types['attachment'] );
		unset( $post_types['elementor_library'] ); // Elementor templates are not a post type we want to track.
		?>

		<h2><?php \esc_html_e( 'Settings', 'progress-planner' ); ?></h2>

		<div class="prpl-widgets-container">
			<div class="prpl-widget-wrapper popup-settings-wrapper">
				<form id="prpl-settings-form">
					<h3><?php \esc_html_e( 'Exclude Post Types', 'progress-planner' ); ?></h3>
					<p><?php \esc_html_e( 'Select the post types you want to exclude from activity scores. This setting will affect which post-type activities get tracked.', 'progress-planner' ); ?></p>
					<?php foreach ( $post_types as $post_type ) : ?>
						<label>
							<input
								type="checkbox"
								name="prpl-settings-post-types-exclude[]"
								value="<?php echo \esc_attr( $post_type ); ?>"
								<?php checked( \in_array( $post_type, $saved_settings, true ) ); ?>
							/>
							<?php echo \esc_html( \get_post_type_object( $post_type )->labels->name ); ?>
						</label>
					<?php endforeach; ?>

					<button id="submit-exclude-post-types" class="button button-primary"><?php \esc_html_e( 'Save', 'progress-planner' ); ?></button>
				</form>
			</div>
		</div>
		<?php
	}
}
