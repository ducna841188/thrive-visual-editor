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
 * Class TCB_Widget_Element
 */
class TCB_Widget_Element extends TCB_Element_Abstract {

	/**
	 * TCB_Widget_Element constructor.
	 *
	 * @param $widget WP_Widget
	 *
	 * @throws Exception
	 */
	public function __construct( $widget ) {

		if ( $widget instanceof WP_Widget ) {

			$tag = 'widget_' . $widget->id_base;

			$this->widget = $widget;

			if ( method_exists( $widget, 'enqueue_admin_scripts' ) ) {
				$widget->enqueue_admin_scripts();
			}

			if ( method_exists( $widget, 'render_control_template_scripts' ) ) {
				$widget->render_control_template_scripts();
			}

			parent::__construct( $tag );
		} else {
			throw new Exception( 'Constructor argument should be a WP_Widget instance' );
		}
	}

	/**
	 * Name of the element
	 *
	 * @return string
	 */
	public function name() {
		$name = $this->widget->name;

		/* some widgets already have 'Widget' at the end of their name */
		if ( strpos( $name, 'Widget' ) === false ) {
			$name = $name . ' ' . __( 'Widget', 'thrive-cb' );
		}

		return $name;
	}

	/**
	 * Return icon class needed for display in menu
	 *
	 * @return string
	 */
	public function icon() {
		return 'wordpress';
	}

	/**
	 * Button element identifier
	 *
	 * @return string
	 */
	public function identifier() {
		return '.thrv_' . $this->widget->id_base;
	}

	/**
	 * @return bool
	 */
	public function has_hover_state() {
		return true;
	}

	/**
	 * Element category that will be displayed in the sidebar
	 *
	 * @return string
	 */
	public function category() {
		return self::get_widgets_label();
	}

	/**
	 * HTML of the current widget
	 * @return string
	 */
	public function html() {
		return '<div class="thrv_wrapper thrv_widget thrv_' . $this->widget->id_base . ' tcb-empty-widget tcb-elem-placeholder">
					<span class="tcb-inline-placeholder-action with-icon">' . tcb_icon( 'wordpress', true, 'editor' ) . $this->name() . '</span>
				</div>';
	}

	/**
	 * Component and control config
	 *
	 * @return array
	 */
	public function own_components() {
		return array(
			'widget'           => array(
				'config' => array(),
			),
			'styles-templates' => array( 'hidden' => true ),
			'typography'       => array( 'hidden' => true ),
		);
	}
}
