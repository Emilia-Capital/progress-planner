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
		<span class="prpl-ravi-reward-graphic">
			<img src="<?php echo \esc_attr( PROGRESS_PLANNER_URL . '/assets/images/ravis_remarkable_reward.png' ); ?>" alt="<?php \esc_attr_e( 'Ravi\'s remarkable reward', 'progress-planner' ); ?>">
		</span>
	</div>
</div>
