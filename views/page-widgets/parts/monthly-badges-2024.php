<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_monthly_badges_2024 = \Progress_Planner\Badges\Monthly::get_instances( 2024 );

// We need only November and December badges.
$prpl_monthly_badges_2024 = array_slice( $prpl_monthly_badges_2024, 10, 2 );

$prpl_css_class = '';
if ( isset( $args['css_class'] ) ) {
	$prpl_css_class = \esc_attr( $args['css_class'] );
}

$prpl_title_tag = isset( $args['title_tag'] ) ? \esc_attr( $args['title_tag'] ) : 'h3';
?>

<div class="<?php echo \esc_attr( $prpl_css_class ); ?>">
	<<?php echo \esc_html( $prpl_title_tag ); ?> class="prpl-widget-title">
		<?php \esc_html_e( 'Monthly badges 2024', 'progress-planner' ); ?>
	</<?php echo \esc_html( $prpl_title_tag ); ?>>

	<div class="progress-wrapper badge-group-monthly">
		<div class="prpl-badge-row-wrapper">
			<div class="prpl-badge-row-wrapper-inner">
				<?php foreach ( $prpl_monthly_badges_2024 as $prpl_badge ) : ?>
					<span
						class="prpl-badge prpl-badge-<?php echo \esc_attr( $prpl_badge->get_id() ); ?>"
						data-value="<?php echo \esc_attr( $prpl_badge->progress_callback()['progress'] ); ?>"
					>
						<prpl-badge
							complete="<?php echo 100 === (int) $prpl_badge->progress_callback()['progress'] ? 'true' : 'false'; ?>"
							badge-id="<?php echo esc_attr( $prpl_badge->get_id() ); ?>"
						></prpl-badge>
						<p><?php echo \esc_html( $prpl_badge->get_name() ); ?></p>
					</span>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
