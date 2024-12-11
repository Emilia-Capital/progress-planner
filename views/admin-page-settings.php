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
	<h1>
		<span class="icon">
			<?php \progress_planner()->the_asset( 'images/icon_settings.svg' ); ?>
		</span>
		<span>
			<?php esc_html_e( 'Your Progress Planner settings', 'progress-planner' ); ?>
		</span>
	</h1>

	<form id="prpl-settings">
		<div class="prpl-column">
			<div class="prpl-widget-wrapper">
				<h2 class="prpl-settings-section-title">
					<span class="icon">
						<?php \progress_planner()->the_asset( 'images/icon_pages.svg' ); ?>
					</span>
					<span>
						<?php esc_html_e( 'Your pages', 'progress-planner' ); ?>
					</span>
				</h2>
				<p>
					<?php esc_html_e( 'Let us know if you have following pages.', 'progress-planner' ); ?>
				</p>
				<div class="prpl-pages-list">
					<?php
					foreach ( \progress_planner()->get_admin__page_settings()->get_settings() as $prpl_setting ) {
						\progress_planner()->the_view( "setting/{$prpl_setting['type']}.php", [ 'prpl_setting' => $prpl_setting ] );
					}
					?>
				</div>
			</div>
		</div>

		<div class="prpl-column">
			<div class="prpl-widget-wrapper">
				<h2 class="prpl-settings-section-license">
					<span>
						<?php \esc_html_e( 'Your licenses', 'progress-planner' ); ?>
					</span>
				</h2>
				<div class="prpl-license-keys-wrapper">
					<div>
						<label>
							<?php
							$prpl_free_license_key = \get_option( 'progress_planner_license_key', '' );
							if ( 'no-license' === $prpl_free_license_key ) {
								$prpl_free_license_key = '';
							}
							?>
							<input type="text" value="<?php echo \esc_attr( $prpl_free_license_key ); ?>" disabled />
							<?php if ( '' === $prpl_free_license_key ) : ?>
								<a href="<?php echo \esc_url( \admin_url( 'admin.php?page=progress-planner#prpl-popover-subscribe-form-trigger' ) ); ?>">
									<?php \esc_html_e( 'Please register to weekly emails to get a free license.', 'progress-planner' ); ?>
								</a>
							<?php else : ?>
								<?php \esc_html_e( 'You automatically received a free license when you registered to weekly emails.', 'progress-planner' ); ?>
							<?php endif; ?>
						</label>
					</div>
					<div>
						<label>
							<?php
							$prpl_pro_license        = \get_option( 'progress_planner_pro_license_key', '' );
							$prpl_pro_license_status = \get_option( 'progress_planner_pro_license_status', '' );
							?>
							<input
								id="prpl-setting-pro-license-key"
								name="prpl-pro-license-key"
								type="text"
								value="<?php echo \esc_attr( $prpl_pro_license ); ?>"
							/>
							<?php \esc_html_e( 'Progress Planner Pro', 'progress-planner' ); ?>
							<?php if ( ! empty( $prpl_pro_license ) ) : ?>
								<span class="prpl-license-status prpl-license-status-<?php echo ( 'valid' === $prpl_pro_license_status ) ? 'valid' : 'invalid'; ?>">
									<?php if ( 'valid' === $prpl_pro_license_status ) : ?>
										<span class="prpl-license-status-valid" title="<?php esc_attr_e( 'Valid', 'progress-planner' ); ?>">
											<?php \progress_planner()->the_asset( 'images/icon_check_circle.svg' ); ?>
										</span>
									<?php else : ?>
										<span class="prpl-license-status-invalid" title="<?php esc_attr_e( 'Invalid', 'progress-planner' ); ?>">
											<?php \progress_planner()->the_asset( 'images/icon_exclamation_circle.svg' ); ?>
										</span>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						</label>
					</div>
				</div>
			</div>
		</div>

		<?php wp_nonce_field( 'prpl-settings' ); ?>

		<button
			id="prpl-settings-submit"
			class="prpl-button-primary"
			type="button"
			style="display:block;width:min-content;"
		>
			<?php esc_attr_e( 'Save', 'progress-planner' ); ?>
		</button>
	</form>
</div>
