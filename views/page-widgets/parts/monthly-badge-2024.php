<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_title_tag = isset( $args['title_tag'] ) ? \esc_attr( $args['title_tag'] ) : 'h3';
?>

<div class="<?php echo \esc_attr( isset( $args['css_class'] ) ? \esc_attr( $args['css_class'] ) : '' ); ?>">
	<<?php echo \esc_html( $prpl_title_tag ); ?> class="prpl-widget-title">
		<?php \esc_html_e( 'Your monthly badge 2024', 'progress-planner' ); ?>
	</<?php echo \esc_html( $prpl_title_tag ); ?>>
	<div class="prpl-ravi-reward-container">
		<span class="prpl-ravi-reward-icon">
			<?php \progress_planner()->the_asset( 'images/badges/monthly-badge-default.svg' ); ?>
		</span>
		<p><?php esc_html_e( 'Ravi\'s remarkable Reward', 'progress-planner' ); ?></p>
	</div>
</div>
