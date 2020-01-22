<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package TCB2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Class TCB_Lead_Generation_Element
 */
class TCB_Lead_Generation_Element extends TCB_Element_Abstract {

	/**
	 * @return string
	 */
	public function name() {
		return __( 'Lead Generation', 'thrive-cb' );
	}

	/**
	 * Get element alternate
	 *
	 * @return string
	 */
	public function alternate() {
		return 'form';
	}

	/**
	 * @return string
	 */
	public function icon() {
		return 'lead_gen';
	}

	/**
	 * @return string
	 */
	public function identifier() {
		return '.thrv_lead_generation';
	}

	/**
	 * @return string
	 */
	public function get_captcha_site_key() {

		$credentials = Thrive_Dash_List_Manager::credentials( 'recaptcha' );

		return ! empty( $credentials['site_key'] ) ? $credentials['site_key'] : '';
	}

	/**
	 * @return array
	 */
	public function own_components() {
		$lead_generation = array(
			'lead_generation' => array(
				'config' => array(
					'connectionType'      => array(
						'config' => array(
							'name'    => __( 'Connection', 'thrive-cb' ),
							'buttons' => array(
								array(
									'text'    => 'API',
									'value'   => 'api',
									'default' => true,
								),
								array(
									'text'  => 'HTML Code',
									'value' => 'custom-html',
								),
							),
						),
					),
					'FieldsControl'       => array(
						'config' => array(
							'sortable'      => true,
							'settings_icon' => 'pen-light',
						),
					),
					'HiddenFieldsControl' => array(
						'config'  => array(
							'sortable' => false,
							'settings_icon' => 'pen-light',
						),
						'extends' => 'PreviewList',
					),
					'ApiConnections'      => array(
						'config' => array(),
					),
					'Captcha'             => array(
						'config'  => array(
							'name'     => '',
							'label'    => __( 'Captcha Spam Prevention', 'thrive-cb' ),
							'default'  => false,
							'site_key' => $this->get_captcha_site_key(),
						),
						'extends' => 'Switch',
					),
					'CaptchaTheme'        => array(
						'config'  => array(
							'name'    => __( 'Theme', 'thrive-cb' ),
							'options' => array(
								array(
									'value' => 'light',
									'name'  => __( 'Light', 'thrive-cb' ),
								),
								array(
									'value' => 'dark',
									'name'  => __( 'Dark', 'thrive-cb' ),
								),
							),
						),
						'extends' => 'Select',
					),
					'CaptchaType'         => array(
						'config'  => array(
							'name'    => __( 'Type', 'thrive-cb' ),
							'options' => array(
								array(
									'value' => 'image',
									'name'  => __( 'Image', 'thrive-cb' ),
								),
								array(
									'value' => 'audio',
									'name'  => __( 'Audio', 'thrive-cb' ),
								),
							),
						),
						'extends' => 'Select',
					),
					'CaptchaSize'         => array(
						'config'  => array(
							'name'    => __( 'Size', 'thrive-cb' ),
							'options' => array(
								array(
									'value' => 'normal',
									'name'  => __( 'Normal', 'thrive-cb' ),
								),
								array(
									'value' => 'compact',
									'name'  => __( 'Compact', 'thrive-cb' ),
								),
							),
						),
						'extends' => 'Select',
					),
					'Consent'             => array(
						'config'  => array(
							'label' => __( 'Explicit Consent Checkbox', 'thrive-cb' ),
						),
						'extends' => 'Switch',
					),
				),
			),
			'typography'      => array(
				'hidden' => true,
			),
			'layout'          => array(
				'disabled_controls' => array(
					'.tve-advanced-controls',
				),
				'config'            => array(
					'Width' => array(
						'important' => true,
					),
				),
			),
			'borders'         => array(
				'disabled_controls' => array(),
			),
			'animation'       => array(
				'hidden' => true,
			),
			'shadow'          => array(
				'config' => array(
					'disabled_controls' => array( 'text' ),
				),
			),
		);

		return array_merge( $lead_generation, $this->group_component() );
	}

	/**
	 * Element category that will be displayed in the sidebar
	 *
	 * @return string
	 */
	public function category() {
		return $this->get_thrive_advanced_label();
	}

	/**
	 * Group Edit Properties
	 *
	 * @return array|bool
	 */
	public function has_group_editing() {
		return array(
			'select_values' => array(
				array(
					'value'    => 'all_lead_gen_items',
					'selector' => '.tve_lg_input',
					'name'     => __( 'Grouped Lead Generation Inputs', 'thrive-cb' ),
					'singular' => __( '-- Input %s', 'thrive-cb' ),
				),
			),
		);
	}
}
