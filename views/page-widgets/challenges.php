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

<h3><?php echo \esc_html( $prpl_challenge['name'] ); ?></h3>
<h4><?php \esc_html_e( 'Description', 'progress-planner' ); ?></h4>
<div><?php echo \wp_kses_post( $prpl_challenge['short_description'] ); ?></div>

<h4><?php \esc_html_e( 'Content', 'progress-planner' ); ?></h4>
<div><?php echo \wp_kses_post( $prpl_challenge['content'] ); ?></div>

<h3><?php \esc_html_e( 'Dates', 'progress-planner' ); ?></h3>
<table>
	<tr>
		<th><?php \esc_html_e( 'Start date', 'progress-planner' ); ?></th>
		<td><?php echo \esc_html( \DateTime::createFromFormat( 'd/m/Y', $prpl_challenge['start_date'] )->format( \get_option( 'date_format' ) ) ); ?></td>
	</tr>
	<tr>
		<th><?php \esc_html_e( 'End date', 'progress-planner' ); ?></th>
		<td><?php echo \esc_html( \DateTime::createFromFormat( 'd/m/Y', $prpl_challenge['end_date'] )->format( \get_option( 'date_format' ) ) ); ?></td>
	</tr>
</table>

<?php if ( $prpl_challenge['webinars'] ) : ?>
	<table>
		<thead>
			<tr>
				<th><?php \esc_html_e( 'Webinar', 'progress-planner' ); ?></th>
				<th><?php \esc_html_e( 'Date', 'progress-planner' ); ?></th>
				<th><?php \esc_html_e( 'Time', 'progress-planner' ); ?></th>
				<th><?php \esc_html_e( 'Registration link', 'progress-planner' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $prpl_challenge['webinars'] as $prpl_webinar ) : ?>
				<?php $prpl_webinar_datetime = \DateTime::createFromFormat( 'd/m/Y g:i a', $prpl_webinar['datetime'] ); ?>
				<tr>
					<td><?php echo \esc_html( $prpl_webinar['name'] ); ?></td>
					<td><?php echo \esc_html( $prpl_webinar_datetime->format( \get_option( 'date_format' ) ) ); ?></td>
					<td><?php echo \esc_html( $prpl_webinar_datetime->format( \get_option( 'time_format' ) ) ); ?></td>
					<td>
						<a href="<?php echo \esc_attr( $prpl_webinar['registration_link']['url'] ); ?>" target="_blank">
							<?php echo \esc_html( $prpl_webinar['registration_link']['title'] ); ?>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		</tr>
	</table>
<?php endif; ?>

<?php if ( $prpl_challenge['associated_post'] ) : ?>
	<h4><?php \esc_html_e( 'Associated posts', 'progress-planner' ); ?></h4>
	<ol>
		<?php foreach ( $prpl_challenge['associated_post'] as $prpl_associated_post ) : ?>
			<li>
				<a href="<?php echo \esc_attr( $prpl_associated_post['url'] ); ?>" target="_blank">
					<?php echo \esc_html( $prpl_associated_post['name'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>
