<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

global $post;

if ( ! empty( $post ) ) {
	$tags = get_the_tags( $post->ID );
}

if ( empty( $tags ) ) {
	echo TCB_Editor()->is_inner_frame() || TCB_Utils::is_rest() ? __( 'No Tags', 'thrive-cb' ) : '';
} else {
	$tags = array_map( function ( $tag ) use ( $data, $post ) {
		$url = get_tag_link( $tag->term_id );

		return empty( $url ) || empty( $data['link'] )
			? $tag->name
			: '<a href="' . $url . '" title="' . $tag->name . '" target="_blank" data-css="' . ( empty( $data['css'] ) ? '' : $data['css'] ) . '">' . $tag->name . '</a>';
	}, $tags );

	echo implode( ', ', $tags );
}
