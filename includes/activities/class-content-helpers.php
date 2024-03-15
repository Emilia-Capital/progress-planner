<?php
/**
 * Handler for posts activities.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

use ProgressPlanner\Activities\Content;
use ProgressPlanner\Date;

/**
 * Handler for posts activities.
 */
class Content_Helpers {

	/**
	 * Get an array of post-types names for the stats.
	 *
	 * @return string[]
	 */
	public static function get_post_types_names() {
		$post_types = \get_post_types( [ 'public' => true ] );
		unset( $post_types['attachment'] );

		return array_keys( $post_types );
	}

	/**
	 * Get words count from content.
	 *
	 * This method will render shortcodes, blocks,
	 * and strip HTML before counting the words.
	 *
	 * @param string $content The content.
	 * @param int    $post_id The post ID. Used for caching the number of words per post.
	 *
	 * @return int
	 */
	public static function get_word_count( $content, $post_id = 0 ) {
		static $counts = [];
		if ( $post_id && isset( $counts[ $post_id ] ) ) {
			return $counts[ $post_id ];
		}

		// Parse blocks and shortcodes.
		$content = \do_blocks( \do_shortcode( $content ) );

		// Strip HTML.
		$content = \wp_strip_all_tags( $content, true );

		$count = \str_word_count( $content );

		if ( $post_id ) {
			$counts[ $post_id ] = $count;
		}

		return $count;
	}

	/**
	 * Get Activity from WP_Post object.
	 *
	 * @param \WP_Post $post The post object.
	 *
	 * @return \ProgressPlanner\Activities\Content
	 */
	public static function get_activity_from_post( $post ) {
		$type = 'publish' === $post->post_status ? 'publish' : 'update';
		$date = 'publish' === $post->post_status ? $post->post_date : $post->post_modified;

		$activity = new Content();
		$activity->set_category( 'content' );
		$activity->set_type( $type );
		$activity->set_date( Date::get_datetime_from_mysql_date( $date ) );
		$activity->set_data_id( $post->ID );
		$activity->set_user_id( $post->post_author );
		return $activity;
	}
}
