<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package TCB2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

class TCB_Lead_Generation_Input_Element extends TCB_Element_Abstract {

	public function name() {
		return __( 'Lead Generation Input', 'thrive-cb' );
	}

	public function identifier() {
		return '.tve_lg_input';
	}

	public function hide() {
		return true;
	}

	public function own_components() {
		$prefix_config           = tcb_selection_root();
		$controls_default_config = array(
			'css_suffix' => array( ' input', ' select' ),
			'css_prefix' => $prefix_config . ' ',
		);

		return array(
			'lead_generation_input' => array(
				'config' => array(
					'placeholder' => array(
						'config' => array(
							'label' => __( 'Placeholder', 'thrive-cb' ),
						),
					),
					'icon_side'   => array(
						'rem_ic_css_suf' => $controls_default_config['css_suffix'], //Remove Icon Css Suffix
						'css_suffix'     => ' .thrv_icon',
						'css_prefix'     => $prefix_config . ' ',
						'config'         => array(
							'name'    => __( 'Icon Side', 'thrive-cb' ),
							'buttons' => array(
								array(
									'value' => 'left',
									'text'  => __( 'Left', 'thrive-cb' ),
								),
								array(
									'value' => 'right',
									'text'  => __( 'Right', 'thrive-cb' ),
								),
							),
						),
					),
					'required'    => array(
						'config' => array(
							'default' => false,
							'label'   => __( 'Required field' ),
						),
					),
				),
			),
			'typography'            => array(
				'config' => array(
					'FontSize'      => $controls_default_config,
					'FontColor'     => array(
						'important'  => true,
						'css_suffix' => array( ' input', ' input::placeholder', ' select' ),
						'css_prefix' => $prefix_config . ' ',
					),
					'FontFace'      => $controls_default_config,
					'LetterSpacing' => $controls_default_config,
					'LineHeight'    => $controls_default_config,
					'TextAlign'     => $controls_default_config,
					'TextStyle'     => array(
						'css_suffix' => array( ' input', ' input::placeholder', ' select' ),
						'css_prefix' => $prefix_config . ' ',
					),
					'TextTransform' => $controls_default_config,
				),
			),
			'layout'                => array(
				'disabled_controls' => array(
					'Width',
					'Height',
					'Alignment',
					'.tve-advanced-controls',
					'hr',
				),
				'config'            => array(
					'MarginAndPadding' => $controls_default_config,
				),
			),
			'borders'               => array(
				'config' => array(
					'Borders' => $controls_default_config,
					'Corners' => $controls_default_config,
				),
			),
			'animation'             => array(
				'hidden' => true,
			),
			'background'            => array(
				'config' => array(
					'ColorPicker' => $controls_default_config,
					'PreviewList' => $controls_default_config,
				),
			),
			'shadow'                => array(
				'config' => array_merge( $controls_default_config, array( 'default_shadow' => 'none' ) ),
			),
			'styles-templates'      => array(
				'config' => array(
					'to' => 'input',
				),
			),
			'responsive'            => array(
				'hidden' => true,
			),
		);
	}

	/**
	 * @return bool
	 */
	public function has_hover_state() {
		return true;
	}
}
