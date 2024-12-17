<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_challenge = \progress_planner()->get_widgets__challenges()->get_challenges()[0];
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Challenge', 'progress-planner' ); ?>
</h2>

<?php echo \wp_kses_post( $prpl_challenge['content'] ); ?>

<div class="prpl-challenge-steps-header">
	<?php foreach ( $prpl_challenge['steps'] as $prpl_challenge_step_index => $prpl_challenge_step ) : ?>
		<button type="button" data-step="<?php echo (int) $prpl_challenge_step_index + 1; ?>">
			<?php echo (int) $prpl_challenge_step_index + 1; ?>
		</button>
	<?php endforeach; ?>
</div>

<div class="prpl-challenge-steps-content">
	<?php foreach ( $prpl_challenge['steps'] as $prpl_challenge_step_index => $prpl_challenge_step ) : ?>
		<div
			class="prpl-challenge-step"
			data-step="<?php echo (int) $prpl_challenge_step_index + 1; ?>"
			style="<?php echo (int) $prpl_challenge_step_index === 0 ? 'display: block;' : 'display: none;'; ?>"
		>
			<h3><?php echo \esc_html( $prpl_challenge_step['title'] ); ?></h3>

			<table>
				<tr>
					<th><?php \esc_html_e( 'Start Date', 'progress-planner' ); ?></th>
					<td><?php echo \esc_html( $prpl_challenge_step['start_date'] ); ?></td>
				</tr>
				<tr>
					<th><?php \esc_html_e( 'End Date', 'progress-planner' ); ?></th>
					<td><?php echo \esc_html( $prpl_challenge_step['end_date'] ); ?></td>
				</tr>
			</table>

			<div class="prpl-challenge-step-content">
				<?php echo \wp_kses_post( $prpl_challenge_step['description'] ); ?>
			</div>

			<div class="prpl-challenge-step-webinar">
				<?php if ( $prpl_challenge_step['webinar_title'] ) : ?>
					<h4><?php echo \esc_html( $prpl_challenge_step['webinar_title'] ); ?></h4>
					<?php $prpl_webinar_datetime = \DateTime::createFromFormat( 'd/m/Y g:i a', $prpl_challenge_step['webinar_datetime'] ); ?>
					<p>
						<?php
						printf(
							/* translators: %1$s: date, %2$s: time */
							\esc_html__( '%1$s at %2$s', 'progress-planner' ),
							\esc_html( $prpl_webinar_datetime->format( \get_option( 'date_format' ) ) ),
							\esc_html( $prpl_webinar_datetime->format( \get_option( 'time_format' ) ) )
						);
						?>
					</p>
					<p>
						<a href="<?php echo \esc_attr( $prpl_challenge_step['webinar_registration_link']['url'] ); ?>" target="_blank">
							<?php \esc_html_e( 'Register', 'progress-planner' ); ?>
						</a>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( $prpl_challenge_step['related_post'] ) : ?>
				<div class="prpl-challenge-step-related-posts">
					<h4><?php \esc_html_e( 'Learn more:', 'progress-planner' ); ?></h4>
					<ul>
						<?php foreach ( $prpl_challenge_step['related_post'] as $prpl_related_post ) : ?>
							<li>
								<a href="<?php echo \esc_attr( $prpl_related_post['url'] ); ?>" target="_blank">
									<?php echo \esc_html( $prpl_related_post['name'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
