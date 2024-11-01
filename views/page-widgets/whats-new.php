<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget = \progress_planner()->get_admin__page()->get_widget( 'whats-new' );

?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'What\'s new on the Progress Planner blog', 'progress-planner' ); ?>
</h2>

<ul>
	<?php foreach ( $prpl_widget->get_blog_feed() as $blog_post ) : ?>
		<li>
			<a href="<?php echo \esc_url( $blog_post['link'] ); ?>" target="_blank">
				<h3><?php echo \esc_html( $blog_post['title']['rendered'] ); ?></h3>
				<?php if ( isset( $blog_post['featured_media']['media_details']['sizes']['medium_large']['source_url'] ) ) : ?>
					<div class="prpl-blog-post-image" style="background-image:url(<?php echo \esc_url( $blog_post['featured_media']['media_details']['sizes']['medium_large']['source_url'] ); ?>)"></div>
				<?php endif; ?>
			</a>
			<p>
				<?php echo \esc_html( \wp_trim_words( \wp_strip_all_tags( $blog_post['content']['rendered'] ), 55 ) ); ?>
			</p>
			<hr />
		</li>
	<?php endforeach; ?>
</ul>
<div class="prpl-widget-footer">
	<a href="https://prpl.fyi/blog" target="_blank">
		<?php \esc_html_e( 'Read all posts', 'progress-planner' ); ?>
	</a>
</div>
