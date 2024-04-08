<?php
/**
 * View for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

?>
<div class="wrap prpl-wrap">
	<h1 class="screen-reader-text"><?php \esc_html_e( 'Progress Planner', 'progress-planner' ); ?></h1>
	<?php require 'admin-page-header.php'; ?>

	<?php require 'welcome.php'; ?>

	<div class="prpl-widgets-container">
		<div class="prpl-column-main prpl-column-main-primary">
			<div class="prpl-column prpl-column-first">
				<?php new \ProgressPlanner\Widgets\Website_Activity_Score(); ?>
				<div class="prpl-column prpl-column-two-col">
					<?php new \ProgressPlanner\Widgets\Published_Content_Density(); ?>
					<?php new \ProgressPlanner\Widgets\Published_Words(); ?>
				</div>
				<?php new \ProgressPlanner\Widgets\Published_Content(); ?>
			</div>
		</div>
		<div class="prpl-column-main prpl-column-main-secondary">
			<div class="prpl-column prpl-column-first">
				<?php new \ProgressPlanner\Widgets\Activity_Scores(); ?>
				<?php new \ProgressPlanner\Widgets\Personal_Record_Content(); ?>
				<?php new \ProgressPlanner\Widgets\Plugins(); ?>
				<?php new \ProgressPlanner\Widgets\Badge_Content(); ?>
				<?php new \ProgressPlanner\Widgets\Badge_Streak(); ?>
			</div>
			<div class="prpl-column prpl-column-second">
				<?php new \ProgressPlanner\Widgets\Latest_Badge(); ?>
				<?php new \ProgressPlanner\Widgets\Badges_Progress(); ?>
				<?php new \ProgressPlanner\Widgets\Whats_New(); ?>
			</div>
		</div>
	</div>
	<div id="prpl-popup-body-overlay"></div>
	<div id="prpl-popup-container">
		<div class="prpl-popup-body"></div>
	</div>
</div>
