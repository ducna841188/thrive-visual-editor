<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

$date = $data['date'];

if ( empty( $data['link'] ) ) {
	echo $date;
} else {
	echo '<a href="' . get_month_link( get_the_date( 'Y' ), get_the_date( 'm' ) ) . '" target="_blank" title="' . $date . '">' . $date . '</a>';
}
