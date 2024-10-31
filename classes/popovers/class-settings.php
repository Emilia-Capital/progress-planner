<?php
/**
 * Settings popover.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Popovers;

/**
 * Settings popover.
 */
final class Settings extends Popover {

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
		$saved_settings = \progress_planner()->get_helpers()->content->get_post_types_names();
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
	}
}
