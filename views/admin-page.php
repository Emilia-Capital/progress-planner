<?php
/**
 * The admin page
 *
 * @package Progress_Planner
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap prpl-wrap">
	<h1 class="screen-reader-text"><?php \esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>
	<?php \progress_planner()->the_view( 'admin-page-header.php' ); ?>
	<?php \progress_planner()->the_view( 'welcome.php' ); ?>

	<div class="prpl-widgets-container">
		<?php foreach ( \progress_planner()->get_admin__page()->get_widgets() as $prpl_admin_widget ) : ?>
			<?php $prpl_admin_widget->render(); ?>
		<?php endforeach; ?>
	</div>
</div>
