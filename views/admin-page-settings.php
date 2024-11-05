<?php
/**
 * The Settings screen.
 *
 * @package Progress_Planner
 */

?>

<div class="wrap prpl-wrap prpl-settings-wrap">
	<div class="prpl-header">
		<div class="prpl-header-logo">
			<?php \progress_planner()->the_asset( 'images/logo_progress_planner.svg' ); ?>
		</div>
	</div>

	<h1>
		<?php esc_html_e( 'Your Progress Planner settings', 'progress-planner' ); ?>
	</h1>

	<form id="prpl-settings">

		<div class="prpl-column">
			<div class="prpl-widget-wrapper">
				<!-- <h2 class="prpl-widget-title">
					<?php esc_html_e( 'Your pages', 'progress-planner' ); ?>
				</h2> -->
				<div class="prpl-pages-list">
					<?php foreach ( \progress_planner()->get_admin__page_settings()->get_tabs_settings() as $prpl_tab_key => $prpl_tab ) : ?>
						<?php foreach ( $prpl_tab['settings'] as $prpl_setting ) : ?>
							<?php \progress_planner()->the_view( "setting/{$prpl_setting['type']}.php", [ 'prpl_setting' => $prpl_setting ] ); ?>
						<?php endforeach; ?>
					<?php endforeach; ?>

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
