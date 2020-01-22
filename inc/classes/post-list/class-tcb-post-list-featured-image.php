<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TCB_Post_List_Featured_Image
 */
class TCB_Post_List_Featured_Image {

	const PLACEHOLDER_URL = 'editor/css/images/featured_image.png';

	/**
	 * Get the html for the placeholder of the featured image.
	 *
	 * @return string
	 */
	public static function get_default_url() {
		return tve_editor_url( static::PLACEHOLDER_URL );
	}

	/**
	 * Get the sizes of the post featured image existent in the website
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	public static function get_sizes( $post_id ) {
		$sizes = array();

		if ( has_post_thumbnail( $post_id ) ) {
			$post_thumbnail_id = get_post_thumbnail_id( $post_id );
			$sizes             = self::get_image_sizes( $post_thumbnail_id );
		}

		return $sizes;
	}


	/**
	 * Get the available sizes for a certain image
	 *
	 * @param $thumb_id
	 *
	 * @return array
	 */
	public static function get_image_sizes( $thumb_id ) {
		$sizes        = array();
		$filter_sizes = self::filter_available_sizes();

		$post_thumbnail        = get_post( $thumb_id );
		$data['media_details'] = wp_get_attachment_metadata( $thumb_id );

		if ( ! empty( $data['media_details']['sizes'] ) ) {
			foreach ( $data['media_details']['sizes'] as $size => $size_data ) {
				/**
				 * Avoid calling `wp_get_attachment_image_src` for each size if the result is not taken into account
				 */
				if ( ! isset( $filter_sizes[ $size ] ) ) {
					continue;
				}

				$image_src = wp_get_attachment_image_src( $thumb_id, $size );
				if ( ! $image_src ) {
					continue;
				}

				$size_data['url'] = $image_src[0];
				$sizes[ $size ]   = $size_data;
			}
		}

		/**
		 * We should always have the full size of the uploaded image
		 */
		$full_src = wp_get_attachment_image_src( $thumb_id, 'full' );

		if ( ! empty( $full_src ) ) {
			$sizes['full'] = array(
				'file'      => wp_basename( $full_src[0] ),
				'width'     => $full_src[1],
				'height'    => $full_src[2],
				'mime_type' => $post_thumbnail->post_mime_type,
				'ID'        => $post_thumbnail->ID,
				'url'       => $full_src[0],
				'title'     => $post_thumbnail->post_name,
				'caption'   => $post_thumbnail->post_excerpt,
				'alt'       => get_post_meta( $post_thumbnail->ID, '_wp_attachment_image_alt', true ),
			);
		}

		return $sizes;
	}


	/**
	 * Return only this specific values from the available sizes options
	 *
	 * @return array
	 */
	public static function filter_available_sizes() {
		return apply_filters( 'image_size_names_choose', array(
			'thumbnail' => __( 'Thumbnail', 'thrive-cb' ),
			'medium'    => __( 'Medium', 'thrive-cb' ),
			'large'     => __( 'Large', 'thrive-cb' ),
			'full'      => __( 'Full Size', 'thrive-cb' ),
		) );
	}
}
