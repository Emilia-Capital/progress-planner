<?php
/**
 * The Settings screen.
 *
 * @package Progress_Planner
 */

$prpl_admin         = new \Progress_Planner\Admin\Page_Settings();
$prpl_tabs_settings = $prpl_admin->get_tabs_settings();
$prpl_page_types    = \Progress_Planner\Page_Types::get_page_types();
?>

<div class="wrap prpl-wrap prpl-settings-wrap">
	<div class="prpl-header">
		<div class="prpl-header-logo">
			<?php
			// phpcs:ignore PEAR.Files.IncludingFile.UseRequire
			include PROGRESS_PLANNER_DIR . '/assets/images/logo_progress_planner.svg';
			?>
		</div>
	</div>

	<h1>
		<?php esc_html_e( 'Your Progress Planner settings', 'progress-planner' ); ?>
	</h1>

	<form id="prpl-settings">

		<div class="prpl-column-main prpl-column-main-secondary">
			<div class="prpl-column">
				<div class="prpl-widget-wrapper">
					<h2 class="prpl-widget-title">
						<?php esc_html_e( 'Your pages', 'progress-planner' ); ?>
					</h2>
					<?php foreach ( $prpl_page_types as $prpl_pagetype ) : ?>
						<?php if ( isset( $prpl_tabs_settings[ "page-{$prpl_pagetype['slug']}" ] ) ) : ?>
							<?php
							$prpl_setting = $prpl_tabs_settings[ "page-{$prpl_pagetype['slug']}" ]['settings'][ $prpl_pagetype['slug'] ];

							// phpcs:ignore PEAR.Files.IncludingFile.UseRequire
							include PROGRESS_PLANNER_DIR . "/views/setting/{$prpl_setting['type']}.php";
							?>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php /* So Submit button is not stretched in right column. */ ?>
		<div class="prpl-column-main prpl-column-main-secondary">
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
