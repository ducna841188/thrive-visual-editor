<?php
require_once plugin_dir_path( __FILE__ ) . 'class-tcb-section-element.php';

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

/**
 * Class TCB_Section_Element
 */
class TCB_Lpblock_Element extends TCB_Section_Element {
	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		return __( 'Landing Page Block', 'thrive-cb' );
	}

	/**
	 * Section element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrv-lp-block.thrv-page-section';
	}

	/**
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
		$components = parent::own_components();
		$components = array_merge( array( 'lpblock' => $components['section'] ), $components );

		unset( $components['section'] );
		unset( $components['shared-styles'] );

		$components['lpblock']['config']['MatchColors'] = array(
			'config'  => array(
				'name'  => '',
				'label' => __( 'Match Colors of Landing Page', 'thrive-cb' ),
			),
			'extends' => 'Switch',
		);

		return array_merge( $components, $this->group_component() );
	}
}