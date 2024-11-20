<?php
/**
 * Template part.
 *
 * @package Progress_Planner
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php foreach ( \progress_planner()->get_badges()->get_badges( $prpl_category ) as $prpl_badge ) : ?>
	<?php $prpl_badge_progress = $prpl_badge->get_progress(); ?>
	<span
		class="prpl-badge"
		data-value="<?php echo \esc_attr( $prpl_badge_progress['progress'] ); ?>"
	>
		<div class="inner">
			<prpl-badge
				class="prpl-badge-icon-image <?php echo 100 === (int) $prpl_badge_progress['progress'] ? 'complete' : 'incomplete'; ?>"
				badge-id="<?php echo esc_attr( $prpl_badge->get_id() ); ?>"
			></prpl-badge>
			<?php echo \esc_html( $prpl_badge->get_name() ); ?>
		</div>
		<p><?php echo \esc_html( $prpl_badge->get_description() ); ?></p>
	</span>
<?php endforeach; ?>
