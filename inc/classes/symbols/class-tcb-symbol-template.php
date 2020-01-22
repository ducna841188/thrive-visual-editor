<?php
/**
 * FileName  class-tcb-symbol-template.php.
 *
 * @project  : thrive-visual-editor
 * @developer: Dragos Petcu
 * @company  : BitStone
 */

class TCB_Symbol_Template {

	/**
	 * Render the symbol content
	 *
	 * @param array $config
	 * @param bool  $do_shortcodes
	 *
	 * @return mixed|string
	 */
	public static function render_content( $config = array(), $do_shortcodes = false ) {

		$symbol_id = ( ! empty( $config ) && isset( $config['id'] ) ) ? $config['id'] : get_the_ID();
		$content   = self::content( $symbol_id );

		/* prepare Events configuration */
		tve_parse_events( $content );

		/**
		 * Filter that allows skipping `do_shortcode` in various cases. Example: when exporting a symbol, do_shortcode should NOT be called,
		 * even though `wp_doing_ajax() === true`
		 *
		 * @param bool  $do_shortcodes initial value
		 * @param int   $symbol_id     current symbol ID
		 * @param array $config        configuration object passed to the method
		 *
		 * @return bool whether or not it should execute the shortcode functions
		 */
		$do_shortcodes = apply_filters( 'tcb_symbol_do_shortcodes', wp_doing_ajax() || $do_shortcodes, $symbol_id, $config );

		if ( $do_shortcodes ) {
			$content = shortcode_unautop( $content );
			$content = do_shortcode( $content );

			//apply thrive shortcodes
			$content = tve_thrive_shortcodes( $content, true );

			/* render the content added through WP Editor (element: "WordPress Content") */
			$content = tve_do_wp_shortcodes( $content, is_editor_page() );
		}
		/**
		 * This only needs to be executed on frontend. Do not execute it in the editor page or when ajax-loading the symbols in the editor
		 */
		if ( ! is_editor_page() && ! wp_doing_ajax() ) {
			$content = tve_restore_script_tags( $content );

			/**
			 * Adds the global style node if it's not in the editor page
			 */
			$content .= tve_get_shared_styles( $content );
		}

		$content = apply_filters( 'tcb_symbol_template', $content );

		return $content;
	}

	/**
	 * Include the start of the html content
	 */
	public static function body_open() {
		include TVE_TCB_ROOT_PATH . 'inc/views/symbols/symbol-body-open.php';
	}

	/**
	 * Include the end of the html content
	 */
	public static function body_close() {
		include TVE_TCB_ROOT_PATH . 'inc/views/symbols/symbol-body-close.php';
	}

	/**
	 * Get the content from the symbol
	 *
	 * @param int $symbol_id
	 *
	 * @return mixed|string
	 */
	public static function content( $symbol_id ) {
		$content = get_post_meta( intval( $symbol_id ), 'tve_updated_post', true );

		return apply_filters( 'tcb_symbol_content', $content );
	}

	/**
	 * Get css for symbol
	 *
	 * @param $config
	 *
	 * @return string
	 */
	public static function tcb_symbol_get_css( $config ) {
		$symbol_id  = ( ! empty( $config ) && isset( $config['id'] ) ) ? $config['id'] : 0;
		$symbol_css = trim( get_post_meta( $symbol_id, 'tve_custom_css', true ) );

		/* If we want to change the symbol css just before is being inserted in the page */
		$symbol_css = apply_filters( 'tcb_symbol_css_before', $symbol_css, $symbol_id );

		$css = "<style class='tve-symbol-custom-style'>" . tve_prepare_global_variables_for_front( $symbol_css ) . '</style>';

		return $css;
	}

	/**
	 * Render symbol shortcode content
	 *
	 * @param array $config
	 *
	 * @return string
	 */
	public static function symbol_render_shortcode( $config ) {
		$symbol_id = ( ! empty( $config ) && isset( $config['id'] ) ) ? $config['id'] : 0;

		$post = get_post( $symbol_id );
		if ( $post && $post->post_status === 'publish' ) {
			$content = self::render_content( $config );
			$css     = self::tcb_symbol_get_css( $config );
			$name    = is_editor_page_raw() ? ' data-name="' . esc_attr( $post->post_title ) . '"' : '';

			return '<div class="thrive-shortcode-html thrive-symbol-shortcode"' . $name . self::data_attr( $symbol_id ) . '>' . $css . $content . '</div>';
		}

		return '';
	}

	/**
	 * Return class for symbol element on it's page
	 *
	 * @return array
	 */
	public static function get_edit_symbol_vars() {
		/**
		 * @var TCB_Symbols_Taxonomy
		 */
		global $tcb_symbol_taxonomy;
		$type = $tcb_symbol_taxonomy->get_symbol_type();

		return array(
			'css_class' => ( $type === 'headers' || $type === 'footers' ) ? 'thrv_' . substr( $type, 0, - 1 ) : '',
			'type'      => substr( $type, 0, - 1 ),
		);
	}

	public static function data_attr( $symbol_id ) {
		$globals = get_post_meta( $symbol_id, 'tve_globals', true );
		if ( empty( $globals ) ) {
			$globals = array();
		}
		/**
		 * backwards compat stuff
		 */
		if ( ! isset( $globals['data-tve-scroll'] ) ) {
			$scroll_behaviour = get_post_meta( $symbol_id, 'tcb_scroll_behaviour', true );
			if ( $scroll_behaviour ) {
				if ( $scroll_behaviour !== 'static' ) {
					$globals['data-tve-scroll'] = json_encode( array(
						'disabled' => array(),
						'mode'     => $scroll_behaviour === 'scroll_up' ? 'appear' : 'sticky',
					) );
					update_post_meta( $symbol_id, 'tve_globals', $globals );
				}
				delete_post_meta( $symbol_id, 'tcb_scroll_behaviour' );
			}
		}

		$attr = ' data-symbol-id="' . intval( $symbol_id ) . '"';

		foreach ( $globals as $k => $value ) {
			if ( strpos( $k, 'data-' ) === 0 ) {
				$attr .= ' ' . $k . '="' . esc_attr( $value ) . '"';
			}
		}

		return $attr;
	}
}
