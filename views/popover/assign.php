<?php
/**
 * Template for the `assign` popover.
 *
 * @package ProgressPlannerPro
 */

?>

<button
	class="button page-assign-button"
	type="button"
	popovertarget="<?php echo esc_attr( 'page-assign-' . $prpl_setting['id'] ); ?>"
	popovertargetaction="show"
>
	<svg style="max-height:1em;width:1.2em;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
		<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
	</svg>
	<?php esc_html_e( 'Assign', 'progress-planner' ); ?>
</button>
<div id="<?php echo esc_attr( 'page-assign-' . $prpl_setting['id'] ); ?>" popover>
	<h1>
		<?php
		printf(
			/* translators: %s: Setting title. */
			esc_html__( 'Assign someone to write the %s page', 'progress-planner' ),
			esc_html( $prpl_setting['title'] )
		);
		?>
	</h1>
	<p>
		<?php esc_html_e( 'Please tell us who to assign. We will email them that they have been assigned, and send them a brief for the page.', 'progress-planner' ); ?>
	</p>
	<label>
		<p class="screen-reader-text"><?php esc_html_e( 'Select a user', 'progress-planner' ); ?></p>
		<?php
		wp_dropdown_users(
			[
				'name'             => 'pages[' . esc_attr( $prpl_setting['id'] ) . '][assign-user]',
				'show_option_none' => '&mdash; ' . esc_html__( 'Select user', 'progress-planner' ) . ' &mdash;',
				'capability'       => 'edit_posts',
			]
		);
		?>
	</label>
	<button
		type="button"
		class="button page-assign-button-close"
		popovertarget="<?php echo esc_attr( 'page-assign-' . $prpl_setting['id'] ); ?>"
		popovertargetaction="hide"
	>
		<?php esc_html_e( 'OK', 'progress-planner' ); ?>
	</button>
</div>
