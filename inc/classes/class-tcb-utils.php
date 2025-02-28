<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TCB_Utils
 */
class TCB_Utils {
	/**
	 * Wrap content in tag with id and/or class
	 *
	 * @param              $content
	 * @param string       $tag
	 * @param string       $id
	 * @param string|array $class
	 * @param array        $attr
	 *
	 * @return string
	 */
	public static function wrap_content( $content, $tag = '', $id = '', $class = '', $attr = array() ) {
		$class = is_array( $class ) ? trim( implode( ' ', $class ) ) : $class;

		if ( empty( $tag ) && ! ( empty( $id ) && empty( $class ) ) ) {
			$tag = 'div';
		}

		$attributes = '';
		foreach ( $attr as $key => $value ) {
			/* if the value is null, only add the key ( this is used for attributes that have no value, such as 'disabled', 'checked', etc ) */
			if ( is_null( $value ) ) {
				$attributes .= ' ' . $key;
			} else {
				$attributes .= ' ' . $key . '="' . $value . '"';
			}
		}

		if ( ! empty( $tag ) ) {
			$content = '<' . $tag . ( empty( $id ) ? '' : ' id="' . $id . '"' ) . ( empty( $class ) ? '' : ' class="' . $class . '"' ) . $attributes . '>' . $content . '</' . $tag . '>';
		}

		return $content;
	}

	/**
	 * Get all the banned post types for the post list/grid.
	 *
	 * @return mixed|void
	 */
	public static function get_banned_post_types() {
		$banned_types = array(
			'attachment',
			'revision',
			'nav_menu_item',
			'custom_css',
			'customize_changeset',
			'oembed_cache',
			'project',
			'et_pb_layout',
			'tcb_lightbox',
			'focus_area',
			'thrive_optin',
			'thrive_ad_group',
			'thrive_ad',
			'thrive_slideshow',
			'thrive_slide_item',
			'tve_lead_shortcode',
			'tve_lead_2s_lightbox',
			'tve_form_type',
			'tve_lead_group',
			'tve_lead_1c_signup',
			TCB_CT_POST_TYPE,
			'tcb_symbol',
			'td_nm_notification',
		);

		/**
		 * Filter that other plugins can hook to add / remove ban types from post grid
		 */
		return apply_filters( 'tcb_post_grid_banned_types', $banned_types );
	}

	/**
	 * Get the image source for the id.
	 *
	 * @param        $image_id
	 * @param string $size
	 *
	 * @return mixed
	 */
	public static function get_image_src( $image_id, $size = 'full' ) {
		$image_info = wp_get_attachment_image_src( $image_id, $size );

		return empty( $image_info ) || empty( $image_info[0] ) ? '' : $image_info[0];
	}

	/**
	 * Get the pagination data that we want to localize.
	 *
	 * @return array
	 */
	public static function get_pagination_localized_data() {
		$localized_data = array();

		/* Apply a filter in case we want to add more pagination types from elsewhere. */
		$all_pagination_types = apply_filters( 'tcb_post_list_pagination_types', TCB_Pagination::$all_types );

		foreach ( $all_pagination_types as $type ) {
			$instance = tcb_pagination( $type );

			$localized_data[ $instance->get_type() ] = $instance->get_content();
		}

		/* we need this when we add new post lists to the page and they need a pagination element wrapper */
		$localized_data['pagination_wrapper'] = tcb_pagination( TCB_Pagination::NONE )->render();

		$localized_data['label_formats'] = array(
			'pages' => tcb_template( 'pagination/label-pages.php', null, true ),
			'posts' => tcb_template( 'pagination/label-posts.php', null, true ),
		);

		return $localized_data;
	}

	/**
	 * Adapt the pagination button component that inherits the button component by disabling some controls and adding new controls.
	 *
	 * @param $components
	 *
	 * @return mixed
	 */
	public static function get_pagination_button_config( $components ) {
		$components['pagination_button'] = $components['button'];
		unset( $components['button'] );

		$all_controls = array_keys( $components['pagination_button']['config'] );

		/* disable all the controls except the ones that we want to be enabled */
		$disabled_controls = array_diff( $all_controls, array( 'MasterColor', 'icon_side' ) );

		/* we have to add this manually */
		$disabled_controls = array_merge( $disabled_controls, array( '.tcb-button-link-container' ) );

		$components['pagination_button']['disabled_controls'] = array_values( $disabled_controls );

		$components['pagination_button']['config']['icon_layout'] = array(
			'config'  => array(
				'name'       => __( 'Button Layout', 'thrive-cb' ),
				'full-width' => true,
				'buttons'    => array(
					array(
						'value' => 'text',
						'text'  => __( 'Text Only', 'thrive-cb' ),
					),
					array(
						'value' => 'icon',
						'text'  => __( 'Icon Only', 'thrive-cb' ),
					),
					array(
						'value' => 'text_plus_icon',
						'text'  => __( 'Icon&Text', 'thrive-cb' ),
					),
				),
				/* default is defined here so it can be overwritten by elements that inherit */
				'default'    => 'text',
			),
			'extends' => 'ButtonGroup',
		);

		$components['animation']['disabled_controls'] = array( '.btn-inline.anim-link', '.btn-inline.anim-popup' );

		$components['layout']['disabled_controls'] = array( 'Display', 'Alignment' );

		$components['scroll']        = array( 'hidden' => true );
		$components['responsive']    = array( 'hidden' => true );
		$components['shared-styles'] = array( 'hidden' => true );

		return $components;
	}

	/**
	 * Get the date/time format options for the wordpress date settings, and append a custom setting.
	 * Can return an associative array of key-value pairs, or multiple arrays of name/value.
	 *
	 * @param string $type
	 * @param bool   $key_value_pairs
	 *
	 * @return array
	 */
	public static function get_post_date_format_options( $type = 'date', $key_value_pairs = false ) {

		if ( $type === 'time' ) {
			/**
			 * Filters the default time formats.
			 *
			 * @param string[] Array of default time formats.
			 */
			$formats = array_unique( apply_filters( 'time_formats', array( __( 'g:i a' ), 'g:i A', 'H:i' ) ) );
		} else {
			/**
			 * Filters the default date formats.
			 *
			 * @param string[] Array of default date formats.
			 */
			$formats = array_unique( apply_filters( 'date_formats', array( __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );
		}

		$custom_option_name = __( 'Custom', 'thrive-cb' );

		if ( $key_value_pairs ) {
			foreach ( $formats as $format ) {
				$options[ $format ] = get_the_time( $format );
			}

			$options['custom'] = $custom_option_name;
		} else {
			$options = array_map( function ( $format ) {
				return array(
					'name'  => get_the_time( $format ),
					'value' => $format,
				);
			}, $formats );

			$options[] = array( 'name' => $custom_option_name, 'value' => '' );
		}

		return $options;
	}

	/**
	 * Get some inline shortcodes for the Pagination Label element.
	 *
	 * @return array
	 */
	public static function get_pagination_inline_shortcodes() {
		return array(
			'Post List Pagination' => array(
				array(
					'option' => __( 'Current page number', 'thrive-cb' ),
					'value'  => 'tcb_pagination_current_page',
				),
				array(
					'option' => __( 'Total number of pages', 'thrive-cb' ),
					'value'  => 'tcb_pagination_total_pages',
				),
				array(
					'option' => __( 'Number of posts on this page', 'thrive-cb' ),
					'value'  => 'tcb_pagination_current_posts',
				),
				array(
					'option' => __( 'Total number of posts', 'thrive-cb' ),
					'value'  => 'tcb_pagination_total_posts',
				),
			),
		);
	}

	/**
	 * Get inline shortcodes for the Post List element.
	 *
	 * @return array
	 */
	public static function get_post_list_inline_shortcodes() {
		$date_format_options = static::get_post_date_format_options( 'date', true );
		$date_formats        = array_keys( $date_format_options );

		$time_format_options = static::get_post_date_format_options( 'time', true );

		return array(
			'Post'      => array(
				array(
					'name'   => __( 'Post Title', 'thrive-cb' ),
					'option' => __( 'Post Title', 'thrive-cb' ),
					'value'  => 'tcb_post_title',
				),
				array(
					'name'   => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ),
					'option' => __( 'Post Date', 'thrive-cb' ),
					'value'  => 'tcb_post_published_date',
					'input'  => array(
						'type'               => array(
							'type'  => 'select',
							'label' => __( 'Display', 'thrive-cb' ),
							'value' => array(
								'published' => __( 'Published Date', 'thrive-cb' ),
								'modified'  => __( 'Modified Date', 'thrive-cb' ),
							),
						),
						'date-format-select' => array(
							'type'  => 'select',
							'label' => __( 'Date Format', 'thrive-cb' ),
							'value' => $date_format_options,
						),
						'date-format'        => array(
							'type'  => 'input',
							'label' => __( 'Format String', 'thrive-cb' ),
							'value' => $date_formats[0],
						),
						'show-time'          => array(
							'type'  => 'checkbox',
							'label' => __( 'Show Time?', 'thrive-cb' ),
							'value' => false,
						),
						'time-format-select' => array(
							'type'  => 'select',
							'label' => __( 'Time Format', 'thrive-cb' ),
							'value' => $time_format_options,
						),
						'time-format'        => array(
							'type'  => 'input',
							'label' => __( 'Format String', 'thrive-cb' ),
							'value' => '',
						),
					),
				),
				array(
					'name'   => __( 'Author Name', 'thrive-cb' ),
					'option' => __( 'Author Name', 'thrive-cb' ),
					'value'  => 'tcb_post_author_name',
				),
				array(
					'name'   => __( 'Author Role', 'thrive-cb' ),
					'option' => __( 'Author Role', 'thrive-cb' ),
					'value'  => 'tcb_post_author_role',
				),
				array(
					'name'   => __( 'Author Bio', 'thrive-cb' ),
					'option' => __( 'Author Bio', 'thrive-cb' ),
					'value'  => 'tcb_post_author_bio',
				),
			),
			'Meta Data' => array(
				array(
					'name'   => __( 'Tag-1, Tag-2, Tag-3', 'thrive-cb' ),
					'option' => __( 'Post Tags', 'thrive-cb' ),
					'value'  => 'tcb_post_tags',
					'input'  => array(
						'link' => array(
							'type'  => 'checkbox',
							'label' => __( 'Link to Archive', 'thrive-cb' ),
							'value' => true,
						),
					),
				),
				array(
					'name'   => __( 'Category-1, Category-2, Category-3', 'thrive-cb' ),
					'option' => __( 'Post Categories', 'thrive-cb' ),
					'value'  => 'tcb_post_categories',
					'input'  => array(
						'link' => array(
							'type'  => 'checkbox',
							'label' => __( 'Link to Archive', 'thrive-cb' ),
							'value' => true,
						),
					),
				),
				array(
					'name'   => 24,
					'option' => __( 'Comments Number', 'thrive-cb' ),
					'value'  => 'tcb_post_comments_number',
				),
			),
		);
	}

	/**
	 * Return the post formats supported by the current theme.
	 *
	 * @return array|mixed
	 */
	public static function get_supported_post_formats() {
		$post_formats = array();

		if ( current_theme_supports( 'post-formats' ) ) {
			$post_formats = get_theme_support( 'post-formats' );

			if ( is_array( $post_formats[0] ) ) {
				$post_formats = $post_formats[0];
			}
		}

		return $post_formats;
	}

	/**
	 * Return the preview URL for this post( symbol or anything that has post meta ) ID along with height/width.
	 * If no URL is found, this can return a placeholder, if one was provided through the parameter.
	 *
	 * @param int    $post_id
	 * @param string $sub_path
	 * @param array  $placeholder_data
	 *
	 * @return array|mixed
	 */
	public static function get_thumb_data( $post_id, $sub_path, $placeholder_data = array() ) {

		$upload_dir = wp_upload_dir();
		$path       = $sub_path . '/' . $post_id . '.png';

		$thumb_path = static::get_uploads_path( $path, $upload_dir );
		$thumb_url  = trailingslashit( $upload_dir['baseurl'] ) . $path;

		/* check if we have preview data in the post meta */
		$thumb_data = static::get_thumbnail_data_from_id( $post_id );

		/* if the post meta is empty, look inside the file and get the data directly from the it */
		if ( empty( $thumb_data['url'] ) ) {
			if ( file_exists( $thumb_path ) ) {
				list( $width, $height ) = getimagesize( $thumb_path );

				$thumb_data = array(
					'url' => $thumb_url,
					'h'   => $height,
					'w'   => $width,
				);
			} else {
				/* if no file is found and no placeholder is provided, return all the values set to blank */
				if ( empty( $placeholder_data ) ) {
					$thumb_data = array(
						'url' => '',
						'h'   => '',
						'w'   => '',
					);
				} else {
					/* if a placeholder is provided, use it */
					$thumb_data = $placeholder_data;
				}
			}
		}

		return $thumb_data;
	}

	/**
	 * Return the uploads physical path.
	 * Things can be appended to it by providing something in $path.
	 *
	 * @param string $path
	 * @param array  $upload_dir
	 *
	 * @return string
	 */
	public static function get_uploads_path( $path = '', $upload_dir = array() ) {
		if ( empty( $upload_dir ) ) {
			$upload_dir = wp_upload_dir();
		}

		return trailingslashit( $upload_dir['basedir'] ) . $path;
	}

	/**
	 * Retrieve the image metadata for the provided post ID.
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	public static function get_thumbnail_data_from_id( $post_id ) {
		return get_post_meta( $post_id, TCB_THUMBNAIL_META_KEY, true );
	}

	/**
	 * Set the image metadata for the provided post ID.
	 *
	 * @param $post_id
	 * @param $thumb_data
	 */
	public static function save_thumbnail_data( $post_id, $thumb_data ) {
		update_post_meta( $post_id, TCB_THUMBNAIL_META_KEY, $thumb_data );
	}

	/**
	 * Check if we're inside the editor and filter the result.
	 *
	 * @param boolean $ajax_check
	 *
	 * @return mixed|void
	 */
	public static function in_editor_render( $ajax_check = false ) {
		return apply_filters( 'tcb_in_editor_render', is_editor_page_raw( $ajax_check ) );
	}

	/**
	 * Check if we're in a REST request.
	 *
	 * @return bool
	 */
	public static function is_rest() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}
}
