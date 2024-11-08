<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title_tag = isset( $args['title_tag'] ) ? \esc_attr( $args['title_tag'] ) : 'h3';
$css_class = isset( $args['css_class'] ) ? \esc_attr( $args['css_class'] ) : '';
?>

<div class="<?php echo \esc_attr( $css_class ); ?>">
<<?php echo \esc_html( $title_tag ); ?> class="prpl-widget-title">
		<?php \esc_html_e( 'Your monthly badge 2024', 'progress-planner' ); ?>
	</<?php echo \esc_html( $title_tag ); ?>>
	<div class="prpl-ravi-reward-container">
		<span class="prpl-ravi-reward-icon">
			<?php \progress_planner()->the_asset( 'images/badges/monthly-badge-default.svg' ); ?>
		</span>
		<p><?php esc_html_e( 'Ravi\'s remarkable Reward', 'progress-planner' ); ?></p>
	</div>
</div>
