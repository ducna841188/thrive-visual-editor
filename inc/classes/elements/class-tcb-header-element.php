<?php
/**
 * FileName  class-tcb-footer-element.php.
 *
 * @project  : thrive-visual-editor
 * @company  : BitStone
 */

/**
 * Class TCB_Footer_Element
 */
class TCB_Header_Element extends TCB_Symbol_Element_Abstract {

	/**
	 * TCB_Header_Element constructor.
	 *
	 * @param string $tag element tag.
	 */
	public function __construct( $tag = '' ) {
		parent::__construct( $tag );
	}

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Thrive Header', 'thrive-cb' );
	}

	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'post_grid';
	}

	/**
	 * Element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrv_symbol.thrv_header';
	}

	/**
	 * Whether or not this element is only a placeholder ( it has no menu, it's not selectable etc )
	 * e.g. Content Templates
	 *
	 * @return bool
	 */
	public function is_placeholder() {
		return false;
	}


	/**
	 * Either to display or not the element in the sidebar menu
	 *
	 * @return bool
	 */
	public function hide() {
		return true;
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		return array(
			'header'     => array(
				'config' => array(
					'HeaderPosition'   => array(
						'config'  => array(
							'name'       => 'Header Position',
							'full-width' => true,
							'buttons'    => array(
								array( 'value' => 'push', 'text' => __( 'Push Content' ), 'default' => true ),
								array( 'value' => 'over', 'text' => __( 'Over Content' ) ),
							),
						),
						'extends' => 'ButtonGroup',
					),
					'HeaderHeight'     => array(
						'config'  => array(
							'default' => '80',
							'min'     => '1',
							'max'     => '1000',
							'label'   => __( 'Content Minimum Height', 'thrive-cb' ),
							'um'      => array( 'px', 'vh' ),
							'css'     => 'min-height',
						),
						'to'      => '.symbol-section-in',
						'extends' => 'Slider',
					),
					'HeaderWidth'      => array(
						'config'  => array(
							'default' => '1024',
							'min'     => '100',
							'max'     => '2000',
							'label'   => __( 'Content Maximum Width', 'thrive-cb' ),
							'um'      => array( 'px', '%' ),
							'css'     => 'max-width',
						),
						'to'      => '.symbol-section-in',
						'extends' => 'Slider',
					),
					'HeaderFullHeight' => array(
						'config'  => array(
							'name'    => '',
							'label'   => __( 'Match height to screen', 'thrive-cb' ),
							'default' => true,
						),
						'to'      => '.symbol-section-in',
						'extends' => 'Switch',
					),
					'VerticalPosition' => array(
						'config'  => array(
							'name'    => __( 'Vertical Position', 'thrive-cb' ),
							'buttons' => array(
								array(
									'icon'    => 'top',
									'default' => true,
									'value'   => '',
								),
								array(
									'icon'  => 'vertical',
									'value' => 'center',
								),
								array(
									'icon'  => 'bot',
									'value' => 'flex-end',
								),
							),
						),
						'to'      => '.symbol-section-in',
						'extends' => 'ButtonGroup',
					),
				),
			),
			'background' => array(
				'config'            => array(
					'to' => '.symbol-section-out',
				),
				'disabled_controls' => array(),
			),
			'shadow'     => array(
				'config' => array(
					'to' => '.symbol-section-out',
				),
			),
			'layout'     => array(
				'disabled_controls' => array( '.tve-advanced-controls', 'Float', 'hr', 'Position', 'PositionFrom', 'zIndex', 'Width', 'Height' ),
				'config'            => array(
					'css_suffix' => ' .thrive-symbol-shortcode',
				),
			),
			'borders'    => array(
				'config' => array(
					'Borders'    => array(),
					'Corners'    => array(),
					'css_suffix' => ' .thrive-symbol-shortcode',
				),
			),
			'typography' => array(
				'disabled_controls' => array(),
				'config'            => array(
					'to' => '.symbol-section-in',
				),
			),
			'decoration' => array(
				'config' => array(
					'to' => '.symbol-section-out',
				),
			),
			'animation'  => array( 'hidden' => true ),
			'scroll'     => array(
				'order'             => 2,
				'config'            => array(
					'to' => '.thrive-symbol-shortcode',
				),
				'disabled_controls' => array( '[data-value="parallax"]' ),
				'hidden'            => false,
			),
		);
	}

	/**
	 * Update meta for scroll on behaviour
	 *
	 * @param $meta
	 *
	 * @return bool
	 */
	public function update_meta( $meta ) {
		$header_id = $meta['header_id'];

		update_post_meta( $header_id, $meta['meta_key'], $meta['meta_value'] );

		return true;
	}
}
