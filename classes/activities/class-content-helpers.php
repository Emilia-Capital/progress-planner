<?php
/**
 * Handler for posts activities.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Activities;

use Progress_Planner\Activities\Content;
use Progress_Planner\Date;
use Progress_Planner\Settings;

/**
 * Handler for posts activities.
 */
class Content_Helpers {

	/**
	 * The threshold for a post to be considered long.
	 *
	 * @var int
	 */
	const LONG_POST_THRESHOLD = 350;

	/**
	 * Get an array of post-types names for the stats.
	 *
	 * @return string[]
	 */
	public static function get_post_types_names() {
		$default            = [ 'post', 'page' ];
		$include_post_types = \array_filter(
			Settings::get( [ 'include_post_types' ], $default ),
			function ( $post_type ) {
				return $post_type && \post_type_exists( $post_type ) && \is_post_type_viewable( $post_type );
			}
		);
		return empty( $include_post_types ) ? $default : \array_values( $include_post_types );
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
		$counts = Settings::get( [ 'word_count' ], [] );
		if ( $post_id && isset( $counts[ $post_id ] ) && false !== $counts[ $post_id ] ) {
			return $counts[ $post_id ];
		}

		if ( empty( $content ) && $post_id ) {
			Settings::set( [ 'word_count', $post_id ], 0 );
			return 0;
		}

		// Parse blocks and shortcodes.
		$content = \do_blocks( \do_shortcode( $content ) );

		// Strip HTML.
		$content = \wp_strip_all_tags( $content, true );

		$count = \str_word_count( $content );

		if ( $post_id && \is_int( $post_id ) ) {
			Settings::set( [ 'word_count', $post_id ], $count );
		}

		return $count;
	}

	/**
	 * Get Activity from WP_Post object.
	 *
	 * @param \WP_Post $post The post object.
	 *
	 * @return \Progress_Planner\Activities\Content
	 */
	public static function get_activity_from_post( $post ) {
		$type = 'publish' === $post->post_status ? 'publish' : 'update';
		$date = 'publish' === $post->post_status ? $post->post_date : $post->post_modified;

		$activity           = new Content();
		$activity->category = 'content';
		$activity->type     = $type;
		$activity->date     = Date::get_datetime_from_mysql_date( $date );
		$activity->data_id  = (string) $post->ID;
		$activity->user_id  = (int) $post->post_author;
		return $activity;
	}

	/**
	 * Figure out if a post is short, or long.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @return bool
	 */
	public static function is_post_long( $post_id ) {
		$word_count = self::get_word_count( \get_post_field( 'post_content', $post_id ), $post_id );
		return $word_count >= self::LONG_POST_THRESHOLD;
	}
}
