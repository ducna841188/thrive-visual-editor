<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

defined( 'TCB_TABLET_THRESHOLD' ) || define( 'TCB_TABLET_THRESHOLD', 768 );
defined( 'TCB_DESKTOP_THRESHOLD' ) || define( 'TCB_DESKTOP_THRESHOLD', 1024 );

/**
 * Class TCB_Logo
 *
 * Implementation of the Logo Element, featuring image optimization and art direction with the <picture> tag - load multiple image versions depending on the device type.
 */
class TCB_Logo {
	/* component name */
	const COMPONENT = 'logo';
	/* identifier used as a class name */
	const IDENTIFIER = 'tcb-logo';
	/* shortcode tag in which the logo HTML is wrapped */
	const SHORTCODE_TAG = 'tcb_logo';
	/* name of the option in the DB where the logo data is stored */
	const OPTION_NAME = 'tcb_logo_data';

	const DELETED_PLACEHOLDER_SRC = 'editor/css/images/logo_deleted_placeholder.png';

	/* list of devices that can have their own logo images ( desktop, tablet, mobile were shortened like this in order to work with mediaAttr() in JS ) */
	private static $all_devices = array( 'd', 't', 'm' );

	/**
	 * TCB_Logo constructor.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Add actions and filters.
	 */
	private function hooks() {
		add_action( 'init', array( $this, 'init_shortcode' ) );

		add_filter( 'tcb_main_frame_localize', array( $this, 'add_localize_params' ) );
		add_filter( 'tcb_content_allowed_shortcodes', array( $this, 'tcb_content_allowed_shortcodes' ) );
	}

	/**
	 * Add data to the main frame localize parameters.
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function add_localize_params( $data ) {
		$logos = static::get_logos();

		$active_logos = array_filter( $logos, function ( $logo ) {
			return (int) $logo['active'] === 1;
		} );

		/* get the src for each attachment ID */
		foreach ( $active_logos as $key => $logo ) {
			$active_logos[ $key ]['src'] = static::get_logo_srcset_or_src( $logo['id'], false );
		};

		$data['logo'] = array(
			'routes'              => array(
				'base'        => get_rest_url( get_current_blog_id(), 'tcb/v1/logo' ),
				'rename_logo' => get_rest_url( get_current_blog_id(), 'tcb/v1/logo/rename_logo' ),
			),
			/* only localize the active logos */
			'sources'             => array_values( $active_logos ),
			'deleted_placeholder' => tve_editor_url( static::DELETED_PLACEHOLDER_SRC ),
		);

		return $data;
	}

	/**
	 * Render the element. Inside the editor, render a simplified version without responsive stuff.
	 * On the frontend, render a <source> tag for each logo chosen for a responsive device screen.
	 *
	 * @param $attr
	 *
	 * @return string
	 */
	public static function render_logo( $attr = array() ) {
		/* if the desktop logo ID is not set, use id = 0 as default and set it in the attr */
		if ( ! isset( $attr['data-id-d'] ) ) {
			$attr['data-id-d'] = 0;
		}

		$desktop_id = (int) $attr['data-id-d'];

		/* se the desktop source as a fallback; get only the src here, since this is an all-browser compatible version */
		$fallback_source = static::get_logo_srcset_or_src( $desktop_id, false );

		/* in the editor or when doing ajax ( when the logo is in a symbol ), return the desktop src only */
		if ( TCB_Utils::in_editor_render() || wp_doing_ajax() ) {
			$content = TCB_Utils::wrap_content( '', 'img', '', '', array( 'src' => $fallback_source ) );
		} else {
			/* if the fallback source is empty and we're outside the editor, return an empty string */
			if ( empty( $fallback_source ) ) {
				$content = '';
			} else {
				$content = static::get_picture_element( $attr, $fallback_source );
			}
		}
		$tag      = apply_filters( 'tcb_logo_tag', 'a' );
		$logo_url = apply_filters( 'tcb_logo_site_url', '' );

		/* We have to process the shortcode here because we cannot send it as a param inside another shortcode ( logo ) */
		if ( ! empty( $attr['data-dynamic-link'] ) ) {
			$attr['href'] = do_shortcode( "[{$attr['data-dynamic-link']} id={$attr['data-shortcode-id']}]" );
		}

		/* embed the img in a link instead of wrapping it in a div (if an url exists) */
		if ( empty( $attr['href'] ) ) {
			$attr['href'] = $logo_url;
		}

		return TCB_Utils::wrap_content( $content, $tag, '', static::get_classes( $attr ), static::get_attr( $attr ) );
	}

	/**
	 * Get the picture element containing the sources. ( For info on how this works, see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/picture )
	 *
	 * @param $attr
	 * @param $fallback_source
	 *
	 * @return string
	 */
	public static function get_picture_element( $attr, $fallback_source ) {
		$picture_content = '';

		foreach ( static::$all_devices as $device ) {
			if ( isset( $attr[ 'data-id-' . $device ] ) ) {
				$id = (int) $attr[ 'data-id-' . $device ];

				/* get the srcset, or, if it doesn't exist, get the source */
				$srcset     = static::get_logo_srcset_or_src( $id );
				$media_attr = array(
					'srcset' => $srcset,
				);

				/* add media rules to restrict where each source is displayed */
				switch ( $device ) {
					case 'd':
						$media_attr['media'] = '(min-width:' . TCB_DESKTOP_THRESHOLD . 'px)';
						break;
					case 't':
						$media_attr['media'] = '(min-width:' . TCB_TABLET_THRESHOLD . 'px) and (max-width:' . TCB_DESKTOP_THRESHOLD . 'px)';
						break;
					case 'm':
						$media_attr['media'] = '(max-width:' . ( TCB_TABLET_THRESHOLD - 1 ) . 'px)';
						break;
					default:
						break;
				}

				$picture_content .= TCB_Utils::wrap_content( '', 'source', '', '', $media_attr );
			}
		}
		/* add the fallback img */
		$picture_content .= TCB_Utils::wrap_content( '', 'img', '', '', array( 'src' => $fallback_source ) );

		/* wrap it in the <picture> tag and return */

		return TCB_Utils::wrap_content( $picture_content, 'picture' );
	}

	/**
	 * @param $id
	 * @param $get_srcset
	 *
	 * @return mixed|string
	 */
	public static function get_logo_srcset_or_src( $id, $get_srcset = true ) {
		/* get the placeholder for this id ( it can differ depending on the ID : 0 and 1 have their own placeholder ) */
		$placeholder = static::get_placeholder_src( $id );

		$index = - 1;

		$logos = static::get_logos();

		/* look for the logo ID in the array of logo data */
		foreach ( $logos as $key => $logo_data ) {
			if ( $id === $logo_data['id'] ) {
				$index = $key;
				break;
			}
		}

		/* if the logo ID was not found, render the placeholder */
		if ( $index === - 1 ) {
			$src = $placeholder;
		} else {
			/* if we found the key for the logo ID, get the src */
			$logo_data = $logos[ $index ];

			/* if the logo is active or it's light or dark, start looking for the image ID */
			if ( $logo_data['active'] || $id === 0 || $id === 1 ) {
				if ( empty( $logo_data['attachment_id'] ) ) {
					$src = $placeholder;
				} else {
					$attachment_id = $logo_data['attachment_id'];

					/* get the full srcset so the browser can pick the best image size for the current screen */
					if ( $get_srcset ) {
						$srcset = wp_get_attachment_image_srcset( $attachment_id );
					}

					/* if srcset is empty (this happens for SVGs and for small images) or we don't want the srcset, return the src instead */
					$src = empty( $srcset ) || ! $get_srcset ? TCB_Utils::get_image_src( $attachment_id ) : $srcset;
				}
			} else {
				/* if the logo isn't light or dark and it's not active, render the placeholder */
				$src = $placeholder;
			}
		}

		return $src;
	}

	/**
	 * Return the placeholder according to the ID.
	 * For id = 0 or 1, return the light/dark placeholder, for any other ID, return a 'logo has been deleted' placeholder.
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public static function get_placeholder_src( $id = 0 ) {
		if ( $id === 0 ) {
			/* light logo placeholder */
			$src = tve_editor_url( 'editor/css/images/logo_placeholder_dark.svg' );
		} elseif ( $id === 1 ) {
			/* dark logo placeholder */
			$src = tve_editor_url( 'editor/css/images/logo_placeholder_light.svg' );
		} else {
			/* If we're in the editor, render a <logo image deleted> image. On the frontend, leave it blank. */
			$src = TCB_Utils::in_editor_render() ? tve_editor_url( static::DELETED_PLACEHOLDER_SRC ) : '';
		}

		return $src;
	}

	/**
	 * Add the two initial logos and their placeholders ( these exist without having to be added manually and cannot be deleted ).
	 */
	public static function initialize_default_logos() {
		$logos = array(
			0 => array(
				'id'            => 0,
				'attachment_id' => '',
				'active'        => 1,
				'default'       => 1,
				'name'          => 'Dark',
			),
			1 => array(
				'id'            => 1,
				'attachment_id' => '',
				'active'        => 1,
				'default'       => 1,
				'name'          => 'Light',
			),
		);
		/* update inside the DB */
		update_option( static::OPTION_NAME, $logos );

		return $logos;
	}

	/**
	 * Get the logo data. ( get_option() is cached, so it's ok to call this lots of times ).
	 *
	 * @return mixed|void
	 */
	public static function get_logos() {
		$logos = get_option( static::OPTION_NAME );

		/* if the option is empty, then we have to initialize the logo array with the default values */
		if ( empty( $logos ) ) {
			/* initialize the default logos - this can't be done earlier because it uses tve_editor_url(), which doesn't exist earlier */
			$logos = static::initialize_default_logos();
		}

		return $logos;
	}

	/**
	 * @param $attr
	 *
	 * @return string
	 */
	private static function get_classes( $attr ) {
		$class = array( static::IDENTIFIER, THRIVE_WRAPPER_CLASS );

		/* set responsive/animation classes, if they are present */
		if ( ! empty( $attr['class'] ) ) {
			$class[] = $attr['class'];
		}

		return implode( ' ', $class );
	}

	/**
	 * @param $attr
	 *
	 * @return array
	 */
	private static function get_attr( $attr ) {
		/* if we're not in the editor or not doing ajax ( for symbols ), remove the logo ID dataset */
		if ( ! TCB_Utils::in_editor_render() && ! wp_doing_ajax() ) {
			foreach ( static::$all_devices as $device ) {
				unset( $attr[ 'data-id-' . $device ] );
			}
		}

		return $attr;
	}

	/**
	 * Add the search shortcode.
	 */
	public function init_shortcode() {
		add_shortcode( static::SHORTCODE_TAG, function ( $attr, $content, $tag ) {
			$attr = TCB_Post_List_Shortcodes::parse_attr( $attr, $tag );

			return TCB_Logo::render_logo( $attr );
		} );
	}

	/**
	 * We need to add our shortcodes to this array in order for them to be processed in the editor.
	 * If we're on the frontend, we don't have to do this.
	 *
	 * @param $shortcodes
	 *
	 * @return array
	 */
	public function tcb_content_allowed_shortcodes( $shortcodes ) {
		if ( is_editor_page_raw( true ) ) {
			$shortcodes = array_merge( $shortcodes, array( static::SHORTCODE_TAG ) );
		}

		return $shortcodes;
	}

	/**
	 * Register REST Routes for the Logo.
	 */
	public static function rest_api_init() {
		require_once TVE_TCB_ROOT_PATH . 'inc/classes/logo/class-tcb-logo-rest.php';
	}
}

new TCB_Logo();
