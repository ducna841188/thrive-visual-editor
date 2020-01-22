<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class TCB_Custom_Fields_Shortcode {
	const GLOBAL_SHORTCODE_URL = 'thrive_custom_fields_shortcode_url';
	const GLOBAL_SHORTCODE_DATA = 'thrive_custom_fields_shortcode_data';

	public static $whitelisted_fields = array(
		'_price',
		'_sale_price',
		'_regular_price',
		'_wc_average_rating',
	);

	public static $blacklisted_fields = array(
		'\_%',
		'thrive%',
		'tcb%',
		'wp%',
		'tve%',
	);

	public static $protected_fields = array(
		'_',                //General protected metadata starts with '_'
		'thrive_',            //Thrive Architect metadata
		'thrv_',
		'tve_',
		'td_nm_',
		'tcb_',
		'tcb2_',
		'tcm_',                //Thrive Comments metadata
		'tva_',                //Thrive Apprentice metadata
		'tu_',                //Thrive Ultimate metadata
		'tqb_',                //Thrive Quiz Builder metadata
		'tvo_',                //Thrive Ovation metadata
		'_tho',                //Thrive Headline Optimiser metadata
		'is_control',        //Thrive Optimize metadata
		/**  Protected Metadata for other plugins**/
		'total_sales',        //WooCommerce metadata
	);

	/**
	 * Holds the User Shortcodes Configuration
	 *
	 * @var array
	 */
	public $user_shortcodes;

	public function __construct() {

		/**
		 * User Shortcodes Configuration
		 */
		$this->user_shortcodes = array(
			'tcb_username_field'   => array(
				'name'             => __( 'Username', 'thrive-cb' ),
				'property'         => 'user_login',
				'not_logged_value' => __( 'Username', 'thrive-cb' ),
			),
			'tcb_first_name_field' => array(
				'name'             => __( 'First Name', 'thrive-cb' ),
				'property'         => 'user_firstname',
				'not_logged_value' => __( 'John', 'thrive-cb' ),
			),
			'tcb_last_name_field'  => array(
				'name'             => __( 'Last Name', 'thrive-cb' ),
				'property'         => 'user_lastname',
				'not_logged_value' => __( 'Doe', 'thrive-cb' ),
			),
		);

		add_filter( 'tcb_content_allowed_shortcodes', array( $this, 'allowed_shortcodes' ) );

		add_filter( 'tcb_dynamiclink_data', array( $this, 'global_links_shortcodes' ) );

		add_shortcode( static::GLOBAL_SHORTCODE_URL, array( $this, 'global_shortcode_url_link' ) );
		add_shortcode( static::GLOBAL_SHORTCODE_DATA, array( $this, 'global_shortcode_url_data' ) );

		foreach ( $this->user_shortcodes as $key => $name ) {
			add_shortcode( $key, array( $this, 'render_user_shortcode' ) );
		}

		add_filter( 'tcb_inline_shortcodes', array( $this, 'tcb_inline_shortcodes' ), 11 );
	}

	/**
	 * Filter allowed shortcodes for tve_do_wp_shortcodes
	 *
	 * @param $shortcodes
	 *
	 * @return array
	 */
	public function allowed_shortcodes( $shortcodes ) {
		return array_merge( $shortcodes, array(
			static::GLOBAL_SHORTCODE_URL,
			static::GLOBAL_SHORTCODE_DATA,
		), array_keys( $this->user_shortcodes )
		);
	}

	/**
	 * Renders the user shortcodes
	 *
	 * @param $attr
	 * @param $content
	 * @param $tag
	 *
	 * @return void|string
	 */
	public function render_user_shortcode( $attr, $content, $tag ) {
		if ( ! is_editor_page_raw( true ) ) {
			$current_user = wp_get_current_user();

			if ( ! empty( $current_user->ID ) ) {
				$prop = $this->user_shortcodes[ $tag ]['property'];

				$return = $current_user->$prop;

				if ( ! empty( $attr['link_to_profile'] ) ) {
					$return = sprintf( '<a href="%s" target="_blank">%s</a>', get_edit_profile_url( $current_user->ID ), $return );
				}
			} else {
				$return = $attr['text_not_logged'];
			}

			return $return;
		}
	}

	/**
	 * Add global shortcodes to be used in dynamic links
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function global_links_shortcodes( $links ) {
		$global_links = array_values( $this->global_custom_links( get_the_ID() ) );

		if ( ! empty( $global_links ) ) {
			$links['Custom Fields Global'] = array( 'links' => array( $global_links ), 'shortcode' => static::GLOBAL_SHORTCODE_URL );
		}

		return $links;
	}

	/**
	 * Global data related to the custom fields with links
	 *
	 * Gets all the custom fields for the current post or post with given id, selects only the fields that correspond to a http link
	 * and returns an array of objects with the structure of a dynamic link
	 *
	 * @param null $post_id
	 *
	 * @return array
	 */
	public function global_custom_links( $post_id = null ) {
		$post_id = $post_id === null ? get_the_ID() : intval( $post_id );
		$custom  = get_post_custom( $post_id );
		//Get all the keys that are not protected meta and are links
		$custom_keys = array_filter( ( array ) get_post_custom_keys( $post_id ), function ( $meta ) use ( $custom ) {
			return apply_filters( 'is_protected_meta', ! filter_var( $custom[ $meta ][0], FILTER_VALIDATE_URL ), $meta, null ) === false;
		} );

		$items = array();
		foreach ( $custom_keys as $val ) {
			$items[ $val ] = array(
				'name' => $val,
				'url'  => $custom[ $val ][0],
				'show' => true,
				'id'   => $post_id . '::' . $val,
			);
		}

		return $items;
	}

	/**
	 * Global data related to the custom fields
	 *
	 * Gets all the custom fields for the current post or post with given id, selects only the fields that do not correspond to a http link
	 * and returns an array of objects with the structure of an inline shortcode
	 *
	 * @param null $post_id
	 *
	 * @return array
	 */
	public function global_custom_metadata( $post_id = null ) {
		$post_id = $post_id === null ? get_the_ID() : intval( $post_id );
		$custom  = get_post_custom( $post_id );
		//Get all the keys that are not protected meta and not links
		$custom_keys = array_filter( ( array ) get_post_custom_keys( $post_id ), function ( $meta ) use ( $custom ) {
			return apply_filters( 'is_protected_meta', filter_var( $custom[ $meta ][0], FILTER_VALIDATE_URL ), $meta, null ) === false;
		} );

		$real_data  = array();
		$value      = array();
		$value_type = array();
		$labels     = array();

		foreach ( $custom_keys as $val ) {
			$key                = $post_id . '::' . $val;
			$real_data[ $key ]  = $custom[ $val ][0]; //Value that will be displayed
			$value[ $key ]      = $val;               //Value appearing as option title
			$value_type[ $key ] = '0';                //Value type (text)

			$labels[ $val ] = static::get_label_for_key( $val, $post_id );
		}

		return array(
			'real_data'  => $real_data,
			'value'      => $value,
			'value_type' => $value_type,
			'labels'     => $labels,
		);
	}

	/**
	 * Get the 'nice' display name for this custom field key.
	 * Each CF plugin seems to have its own way of retrieving these
	 *
	 * @param $val
	 * @param $post_id
	 *
	 * @return string
	 */
	public static function get_label_for_key( $val, $post_id ) {
		$label = '';

		/* this is for ACF custom fields only, more plugins can be covered here */
		if ( function_exists( 'get_field_object' ) ) {
			$field_obj = get_field_object( $val, $post_id );
			if ( ! empty( $field_obj ) && ! empty( $field_obj['label'] ) ) {
				$label = $field_obj['label'];
			}
		}

		return $label;
	}

	/**
	 * Add some inline shortcodes.
	 *
	 * @param $shortcodes
	 *
	 * @return array
	 */
	public function tcb_inline_shortcodes( $shortcodes ) {

		$custom_data_global = $this->global_custom_metadata();

		$custom_shortcodes = array(
			'Meta Data' => array(
				array(
					'name'        => __( 'Custom Fields Global', 'thrive-cb' ),
					'value'       => static::GLOBAL_SHORTCODE_DATA,
					'option'      => __( 'Custom Fields', 'thrive-cb' ),
					'extra_param' => 'CFG',
					'input'       => array(
						'id' => array(
							'extra_options' => array(),
							'label'         => 'Field',
							'real_data'     => $custom_data_global['real_data'],
							'type'          => 'select',
							'value'         => $custom_data_global['value'],
							'value_type'    => $custom_data_global['value_type'],
							'labels'        => $custom_data_global['labels'],
						),
					),
				),
				array(
					'name'        => __( 'Custom Fields Postlist', 'thrive-cb' ),
					'value'       => 'tcb_post_custom_field',
					'option'      => __( 'Custom Fields', 'thrive-cb' ),
					'extra_param' => 'CFP',
					'input'       => array(
						'id' => array(
							'extra_options' => array(),
							'label'         => 'Field',
							'real_data'     => array(),
							'type'          => 'select',
							'value'         => array(),
							'value_type'    => array(),
						),
					),
				),
			),
			'User'      => array(),
		);

		foreach ( $this->user_shortcodes as $key => $option ) {
			$custom_shortcodes['User'][] = array(
				'name'   => $option['name'],
				'value'  => $key,
				'option' => $option['name'],
				'input'  => array(
					'link_to_profile' => array(
						'label' => __( 'Link to user profile', 'thrive-cb' ),
						'type'  => 'checkbox',
					),
					'text_not_logged' => array(
						'label' => __( 'Text if visitor is not logged in', 'thrive-cb' ),
						'type'  => 'input',
						'value' => $option['not_logged_value'],
					),

				),
			);
		}

		return array_merge_recursive( $shortcodes, $custom_shortcodes );

	}

	/**
	 * Replace the shortcode with its content
	 *
	 * @param $args
	 *
	 * @return mixed|string
	 */
	public function global_shortcode_url_link( $args ) {
		$data = '';

		if ( ! empty( $args['id'] ) ) {
			$shortcode_data = static::get_parsed_shortcode_data( $args['id'] );

			$groups = $this->global_custom_links( (int) $shortcode_data['post_id'] );

			if ( isset( $groups[ $shortcode_data['id'] ] ) ) {
				$data = $groups[ $shortcode_data['id'] ]['url'];
			}
		}

		return $data;
	}


	/**
	 * Replace the shortcode with its content
	 *
	 * @param $args
	 *
	 * @return mixed|string
	 */
	public function global_shortcode_url_data( $args ) {
		$data = '';

		if ( ! empty( $args['id'] ) ) {
			$shortcode_data = static::get_parsed_shortcode_data( $args['id'] );

			$groups  = $this->global_custom_metadata( (int) $shortcode_data['post_id'] );
			$full_id = $shortcode_data['post_id'] . '::' . $shortcode_data['id'];

			if ( isset( $groups['real_data'][ $full_id ] ) ) {
				$data = $groups['real_data'][ $full_id ];
			}
		}

		return $data;
	}

	/**
	 * Get the post ID and the shortcode ID from the string. If no post ID exists, use the current post ID.
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public static function get_parsed_shortcode_data( $data ) {
		if ( strpos( $data, '::' ) ) {
			$shortcode_data = explode( '::', $data );

			$post_id      = $shortcode_data[0];
			$shortcode_id = $shortcode_data[1];
		} else {
			/* in certain cases where we don't have an ID ( TTB ), get the current post ID */
			$post_id      = get_the_ID();
			$shortcode_id = $data;
		}

		return array(
			'post_id' => $post_id,
			'id'      => $shortcode_id,
		);
	}
}

new TCB_Custom_Fields_Shortcode();
