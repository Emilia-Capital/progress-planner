<?php
/**
 * Template for the `plan` popover.
 *
 * @package ProgressPlannerPro
 */

?>
<button
	class="button page-plan-button"
	type="button"
	popovertarget="<?php echo esc_attr( 'page-plan-' . $prpl_setting['id'] ); ?>"
	popovertargetaction="show"
>
	<svg style="max-height:1em;width:1.2em;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
		<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
	</svg>
	<?php esc_html_e( 'Plan', 'progress-planner' ); ?>
</button>
<div id="<?php echo esc_attr( 'page-plan-' . $prpl_setting['id'] ); ?>" popover>
	<h1><?php esc_html_e( 'Pick a date', 'progress-planner' ); ?></h1>
	<p>
		<?php
		printf(
			/* translators: %s: Setting title. */
			esc_html__( 'We will remind you about creating your %s page!', 'progress-planner' ),
			esc_html( $prpl_setting['title'] )
		);
		?>
	</p>
	<label>
		<p><?php esc_html_e( 'Select a date', 'progress-planner' ); ?></p>
		<input
			type="date"
			name="pages[<?php echo esc_attr( $prpl_setting['id'] ); ?>][plan-date]"
			value=""
		>
	</label>
	<button
		type="button"
		class="button page-plan-button-close"
		popovertarget="<?php echo esc_attr( 'page-plan-' . $prpl_setting['id'] ); ?>"
		popovertargetaction="hide"
	>
		<?php esc_html_e( 'OK', 'progress-planner' ); ?>
	</button>
</div>
