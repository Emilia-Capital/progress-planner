<?php
/**
 * The Settings screen.
 *
 * @package Progress_Planner
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap prpl-wrap prpl-settings-wrap">
	<div class="prpl-header">
		<div class="prpl-header-logo">
			<?php \progress_planner()->the_asset( 'images/logo_progress_planner.svg' ); ?>
		</div>
	</div>

	<h1><?php esc_html_e( 'Your Progress Planner settings', 'progress-planner' ); ?></h1>

	<form id="prpl-settings">
		<div class="prpl-column">
			<div class="prpl-widget-wrapper">
				<?php esc_html_e( 'Your pages', 'progress-planner' ); ?>
				<div class="prpl-pages-list">
					<?php
					foreach ( \progress_planner()->get_admin__page_settings()->get_settings() as $prpl_setting ) {
						\progress_planner()->the_view( "setting/{$prpl_setting['type']}.php", [ 'prpl_setting' => $prpl_setting ] );
					}
					?>
				</div>
			</div>
		</div>

		<?php wp_nonce_field( 'prpl-settings' ); ?>

		<button
			id="prpl-settings-submit"
			class="button button-primary"
			type="button"
			style="display:block;width:min-content;"
		>
			<?php esc_attr_e( 'Save', 'progress-planner' ); ?>
		</button>
	</form>
</div>
