<?php
/**
 * The Settings screen.
 *
 * @package Progress_Planner
 */

$prpl_tabs_settings = ( new \Progress_Planner\Admin\Page_Settings() )->get_tabs_settings();
$prpl_page_types    = \progress_planner()->get_page_types()->get_page_types();
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
					<?php foreach ( $prpl_tabs_settings as $prpl_tab_key => $prpl_tab ) : ?>

						<div>
							<?php /* phpcs:disable */ ?>
							<?php /* echo esc_html( $tab['title'] ); */ ?>
							<?php /* echo esc_html( $tab['desc'] ); */ ?>

							<?php foreach ( $prpl_tab['settings'] as $prpl_setting ) : ?>

								<?php
									// WIP. Skip radio buttons with page-select.
								if ( false !== strpos( $prpl_setting['id'], 'has-page-' ) ) {
									continue;
								}
								?>

								<div class="prpl-pages-item-setting">
									<?php if ( 'page-select' === $prpl_setting['type'] ) : ?>
										<div
											class="prpl-pages-item prpl-pages-item-<?php echo esc_attr( $prpl_setting['page'] ); ?>"
											data-page-item="<?php echo esc_attr( $prpl_setting['page'] ); ?>"
										>
											<?php \progress_planner()->the_view( "setting/{$prpl_setting['type']}.php", [ 'prpl_setting' => $prpl_setting ] ); ?>
										</div>
									<?php else : ?>
										<div class="prpl-pages-item">
											<?php /* phpcs:disable */ ?>
											<?php /* echo isset( $prpl_setting['title'] ) ? esc_html( $prpl_setting['title'] ) : ''; */ ?>
											<?php /* echo isset( $prpl_setting['label'] ) ? esc_html( $prpl_setting['label'] ) : ''; */ ?>
											<?php /* phpcs:enable */ ?>
											<?php \progress_planner()->the_view( "setting/{$prpl_setting['type']}.php", [ 'prpl_setting' => $prpl_setting ] ); ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
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
