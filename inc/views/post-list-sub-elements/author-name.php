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

$author      = empty( $post ) ? null : $post->post_author;
$author_name = get_the_author_meta( 'display_name', $author );

if ( empty( $author ) ) {
	echo TCB_Editor()->is_inner_frame() || TCB_Utils::is_rest() ? __( 'No Author', 'thrive-cb' ) : '';
} else {
	if ( empty( $data['link'] ) ) {
		echo $author_name;
	} else {
		echo '<a href="' . $data['link'] . '" target="_blank" title="' . $author_name . '">' . $author_name . '</a>';
	}
}
