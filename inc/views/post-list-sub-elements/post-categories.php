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
	$categories = get_the_category( $post->ID );
}

if ( empty( $categories ) ) {
	echo TCB_Editor()->is_inner_frame() || TCB_Utils::is_rest() ? __( 'No Categories', 'thrive-cb' ) : '';
} else {
	$categories = array_map( function ( $category ) use ( $data, $post ) {
		$url = get_category_link( $category->term_id );

		return empty( $url ) || empty( $data['link'] )
			? $category->name
			: '<a href="' . $url . '" title="' . $category->name . '" target="_blank" data-css="' . ( empty( $data['css'] ) ? '' : $data['css'] ) . '">' . $category->name . '</a>';

	}, $categories );

	echo implode( ', ', $categories );
}
