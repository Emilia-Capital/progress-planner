<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

// Get the blog feed.
$prpl_blog_feed = \progress_planner()->get_blog_feed();
?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'What\'s new on the Progress Planner blog', 'progress-planner' ); ?>
</h2>

<ul>
	<?php foreach ( $prpl_blog_feed as $prpl_blog_post ) : ?>
		<li>
			<a href="<?php echo \esc_url( $prpl_blog_post['link'] ); ?>" target="_blank">
				<h3><?php echo \esc_html( $prpl_blog_post['title']['rendered'] ); ?></h3>
				<p>
					<?php echo \esc_html( \wp_strip_all_tags( $prpl_blog_post['excerpt']['rendered'] ) ); ?>
				</p>
			</a>
		</li>
	<?php endforeach; ?>
</ul>

