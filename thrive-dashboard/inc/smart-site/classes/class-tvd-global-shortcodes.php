<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Class TVD_Global_Shortcodes
 */
class TVD_Global_Shortcodes {

	const GLOBAL_SHORTCODE_URL = 'thrive_global_shortcode_url';

	public function __construct() {
		add_filter( 'tcb_content_allowed_shortcodes', array( $this, 'allowed_shortcodes' ) );
		add_filter( 'tcb_dynamiclink_data', array( $this, 'links_shortcodes' ) );
		add_shortcode( static::GLOBAL_SHORTCODE_URL, array( $this, 'global_shortcode_url' ) );
	}

	/**
	 * Filter allowed shortcodes for tve_do_wp_shortcodes
	 *
	 * @param $shortcodes
	 *
	 * @return array
	 */
	public function allowed_shortcodes( $shortcodes ) {
		return array_merge( $shortcodes, array( static::GLOBAL_SHORTCODE_URL ) );
	}

	/**
	 * Add global shortcodes to be used in dynamic links
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function links_shortcodes( $links ) {
		$global_links = array();
		foreach ( $this->global_data() as $index => $value ) {
			$value['id']    = $index;
			$global_links[] = $value;
		}
		$links['Site'] = array( 'links' => array( $global_links ), 'shortcode' => static::GLOBAL_SHORTCODE_URL );

		return $links;
	}

	/**
	 * Global data related to the site
	 *
	 * @return array
	 */
	public function global_data() {
		return apply_filters( 'tvd_global_data', array(
			array(
				'name' => __( 'Homepage', TVE_DASH_TRANSLATE_DOMAIN ),
				'url'  => get_home_url(),
				'show' => true
			),
			array(
				'name' => __( 'Blog', TVE_DASH_TRANSLATE_DOMAIN ),
				'url'  => get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : get_home_url(),
				'show' => true
			),
			array(
				'name' => __( 'RSS Feed', TVE_DASH_TRANSLATE_DOMAIN ),
				'url'  => get_home_url() . '/feed',
				'show' => true
			),
			array(
				'name' => __( 'Login', TVE_DASH_TRANSLATE_DOMAIN ),
				'url'  => wp_login_url(),
				'show' => true
			),
			array(
				'name' => __( 'Logout', TVE_DASH_TRANSLATE_DOMAIN ),
				'url'  => wp_logout_url(),
				'show' => true
			),
		) );
	}

	/**
	 * Replace the shortcode with its content
	 *
	 * @param $args
	 *
	 * @return mixed|string
	 */
	public function global_shortcode_url( $args ) {
		$data = '';
		if ( isset( $args['id'] ) ) {
			$groups = $this->global_data();
			$id     = ( int ) $args['id'];
			$data   = empty( $groups[ $id ] ) ? '' : $groups[ $id ]['url'];
		}

		return $data;
	}
}