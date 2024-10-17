<?php
/**
 * Template for a `page-select` setting.
 *
 * @package Progress_Planner
 */

?>
<div class="prpl-pages-item prpl-pages-item-<?php echo esc_attr( $prpl_setting['page'] ); ?>" data-page-item="<?php echo esc_attr( $prpl_setting['page'] ); ?>">
	<div class="item-description">
		<h3><?php echo esc_html( $prpl_setting['title'] ); ?></h3>
		<p><?php echo esc_html( $prpl_setting['description'] ); ?></p>
	</div>
	<div class="item-actions" data-page="<?php echo esc_attr( $prpl_setting['page'] ); ?>">
		<div data-action="select">
			<?php
			wp_dropdown_pages(
				[
					'name'             => 'pages[' . esc_attr( $prpl_setting['id'] ) . '][id]',
					'show_option_none' => '&mdash; ' . esc_html__( 'Select page', 'progress-planner' ) . ' &mdash;',
					'selected'         => (int) $prpl_setting['value'],
				]
			);
			?>
		</div>
		<div data-action="edit">
			<a
				target="_blank"
				class="button"
				href=""
				data-page="<?php echo esc_attr( $prpl_setting['page'] ); ?>"
			>
				<?php esc_html_e( 'Edit', 'progress-planner' ); ?>
			</a>
		</div>

		<div data-action="create">
			<?php
			/**
			 * TODO: Find a way to assign the post-meta for the new page.
			 */
			?>
			<a
				target="_blank"
				class="button"
				href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>"
			>
				<?php esc_html_e( 'Create', 'progress-planner' ); ?>
			</a>
		</div>
		<div data-action="plan">
			<?php include PROGRESS_PLANNER_DIR . '/views/popover/plan.php'; // phpcs:ignore PEAR.Files.IncludingFile.UseRequire ?>
		</div>
		<div data-action="assign">
			<?php include PROGRESS_PLANNER_DIR . '/views/popover/assign.php'; // phpcs:ignore PEAR.Files.IncludingFile.UseRequire ?>
		</div>
	</div>
</div>
