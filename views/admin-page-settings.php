<?php
/**
 * The Settings screen.
 *
 * @package Progress_Planner
 */

$prpl_tabs_settings = \progress_planner()->get_settings_page()->get_tabs_settings();
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
				<h2 class="prpl-widget-title">
					<?php esc_html_e( 'Your pages', 'progress-planner' ); ?>
				</h2>
				<div class="prpl-pages-list">
					<?php foreach ( $prpl_page_types as $prpl_pagetype ) : ?>
						<?php if ( isset( $prpl_tabs_settings[ "page-{$prpl_pagetype['slug']}" ] ) ) : ?>
							<?php $prpl_setting = $prpl_tabs_settings[ "page-{$prpl_pagetype['slug']}" ]['settings'][ $prpl_pagetype['slug'] ]; ?>
							<div
								class="prpl-pages-item prpl-pages-item-<?php echo esc_attr( $prpl_setting['page'] ); ?>"
								data-page-item="<?php echo esc_attr( $prpl_setting['page'] ); ?>"
							>
								<?php \progress_planner()->the_view( "setting/{$prpl_setting['type']}.php" ); ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
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
