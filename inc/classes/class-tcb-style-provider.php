<?php

/**
 * Default TAr style class. Extend this to modify default style functionality
 *
 * Class TCB_Style_Provider
 */
class TCB_Style_Provider {

	/**
	 * Get an array of default style specifications
	 *
	 * @return array
	 */
	protected function defaults() {
		$defaults = array(
			'link'       => array(
				/* needs to be quite specific. some 3rd party themes are really specific */
				'selector'    => ':not(.inc) .thrv_text_element a, :not(.inc) .tcb-styled-list a, :not(.inc) .tcb-numbered-list a',
				'lp_selector' => '#tcb_landing_page .thrv_text_element a, #tcb_landing_page .tcb-styled-list a, #tcb_landing_page .tcb-numbered-list a',
			),
			'p'          => array(
				'selector'    => '.tcb-style-wrap p',
				'lp_selector' => '#tcb_landing_page p',
			),
			'ul'         => array(
				'selector'    => '.tcb-style-wrap ul, .tcb-style-wrap ol',
				'lp_selector' => '#tcb_landing_page ul, #tcb_landing_page ol',
			),
			'li'         => array(
				'selector'    => '.tcb-style-wrap li',
				'lp_selector' => '#tcb_landing_page li',
			),
			'pre'        => array(
				'selector'    => '.tcb-style-wrap pre',
				'lp_selector' => '#tcb_landing_page pre',
			),
			'blockquote' => array(
				'selector'    => '.tcb-style-wrap blockquote',
				'lp_selector' => '#tcb_landing_page blockquote',
			),
			'plaintext'  => array(
				'selector'    => '.tcb-plain-text',
				'lp_selector' => '.tve_lp .tcb-plain-text',
			),
		);

		foreach ( range( 1, 6 ) as $level ) {
			$defaults[ 'h' . $level ] = array(
				'selector'    => '.tcb-style-wrap h' . $level,
				'lp_selector' => '#tcb_landing_page h' . $level,
			);
		}

		return $defaults;
	}

	/**
	 * Get list of default TAr styles
	 *
	 * @return array
	 */
	public function get_styles() {
		return $this->prepare_styles( $this->read_styles() );
	}

	/**
	 * Prepares the list of styles taken out of the DB, making sure it's valid, it contains all needed selectors etc
	 *
	 * @param array $styles
	 *
	 * @return array
	 */
	protected function prepare_styles( $styles ) {
		$styles   = (array) $styles;
		$defaults = $this->defaults();

		$styles = array_intersect_key( $styles, $defaults ); // cleanup anything unnecessary

		foreach ( $defaults as $type => $style ) {
			$styles[ $type ]['selector']    = $style['selector'];
			$styles[ $type ]['lp_selector'] = $style['lp_selector'];
		}

		return (array) $styles;
	}

	/**
	 * Read styles from data source.
	 * Default: read them from wp_options
	 *
	 * When extending the class, this method should be overridden
	 *
	 * @return array formatted list of styles
	 */
	protected function read_styles() {
		$styles = get_option( 'tve_default_styles', array() );

		/* li gets the same styles as <p> at first, but it's stylable individually in some places */
		if ( empty( $styles['li'] ) ) {
			$styles['li'] = isset( $styles['p'] ) ? $styles['p'] : array();
		}

		return $styles;
	}

	/**
	 * Saves styles to the database
	 *
	 * @param array $styles
	 */
	public function save_styles( $styles ) {
		update_option( 'tve_default_styles', $styles );
	}

	/**
	 * Process raw styles, building and replacing selectors
	 *
	 * @param array  $raw_styles raw styles collected from DB
	 * @param string $return     Can be one of 'string', 'object' used to control how to return the results
	 *
	 * @return array|string array with @imports and media keys, each of them a string
	 *                      or a string containing all CSS
	 */
	public function get_processed_styles( $raw_styles = null, $return = 'object' ) {
		$raw_styles = $this->prepare_styles( isset( $raw_styles ) ? $raw_styles : $this->read_styles() );
		$data       = array(
			'@imports' => isset( $raw_styles['@imports'] ) ? $raw_styles['imports'] : array(),
			'media'    => array(),
		);

		unset( $raw_styles['@imports'] );

		foreach ( $raw_styles as $element_type => $style_data ) {
			$selector        = $style_data['selector'];
			$suffix_selector = str_replace( ', ', '%suffix%, ', $selector ) . '%suffix%';
			if ( isset( $style_data['@imports'] ) ) {
				$data['@imports'] = array_merge( $data['@imports'], $style_data['@imports'] );
			}
			unset( $style_data['selector'], $style_data['@imports'], $style_data['lp_selector'] );
			foreach ( $style_data as $media_key => $css_rules ) {
				$css_rules = implode( '', $css_rules );

				/* make sure suffix selectors are built correctly */
				$css_rules = preg_replace_callback( '#__el__([^{]{2,}?){#m', function ( $matches ) use ( $suffix_selector ) {
					return str_replace( '%suffix%', rtrim( $matches[1] ), $suffix_selector ) . ' {';
				}, $css_rules );

				$data['media'][ $media_key ] = ( isset( $data['media'][ $media_key ] ) ? $data['media'][ $media_key ] : '' ) . str_replace( '__el__', $selector, $css_rules );
			}
		}

		if ( $return === 'string' ) {
			$css = implode( "\n", $data['@imports'] ); // import rules
			foreach ( $data['media'] as $media => $css_str ) {
				$css .= "@media {$media} { $css_str }";
			}

			return $css;
		}

		return $data;
	}
}
