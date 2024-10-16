<?php
/**
 * The Settings screen.
 *
 * @package Progress_Planner
 */

$prpl_admin             = new \Progress_Planner\Admin\Page_Settings();
$prpl_pro_tabs_settings = $prpl_admin->get_tabs_settings();
?>

<div class="wrap prpl-wrap prpl-pro-wrap">
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

	<form id="prpl-pro-settings" class="prpl-widgets-container">
		<div class="prpl-column-main prpl-column-main-primary">
			<div class="prpl-column">
				<?php foreach ( [ 'time_allocation' ] as $prpl_setting_key ) : ?>
					<?php if ( isset( $prpl_pro_tabs_settings['intake']['settings'][ $prpl_setting_key ] ) ) : ?>
					<div class="prpl-widget-wrapper prpl-pro-widget-setting-<?php echo esc_attr( $prpl_setting_key ); ?>">
						<?php $prpl_setting = $prpl_pro_tabs_settings['intake']['settings'][ $prpl_setting_key ]; ?>
						<h2 class="prpl-widget-title"><?php echo esc_html( $prpl_setting['title'] ); ?></h2>
						<?php
						// phpcs:ignore PEAR.Files.IncludingFile.UseRequire
						include PROGRESS_PLANNER_DIR . "/views/setting/{$prpl_setting['type']}.php";
						?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="prpl-column-main prpl-column-main-primary">
			<div class="prpl-column">
				<?php foreach ( [ 'site_type' ] as $prpl_setting_key ) : ?>
					<?php if ( isset( $prpl_pro_tabs_settings['intake']['settings'][ $prpl_setting_key ] ) ) : ?>
					<div class="prpl-widget-wrapper prpl-pro-widget-setting-<?php echo esc_attr( $prpl_setting_key ); ?>">
						<?php $prpl_setting = $prpl_pro_tabs_settings['intake']['settings'][ $prpl_setting_key ]; ?>
						<h2 class="prpl-widget-title"><?php echo esc_html( $prpl_setting['title'] ); ?></h2>
						<?php
						// phpcs:ignore PEAR.Files.IncludingFile.UseRequire
						include PROGRESS_PLANNER_DIR . "/views/setting/{$prpl_setting['type']}.php";
						?>
						</div>
					<?php endif; ?>
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
