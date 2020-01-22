<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

?>
<div id="tve-widget-component" class="tve-component" data-view="Widget">
	<div class="dropdown-header" data-prop="docked">
		<?php echo __( 'Widget', 'thrive-cb' ); ?>
		<i></i>
	</div>
	<div class="dropdown-content">
		<button class="tve-button long click blue" data-fn="widget_settings">
			<?php echo __( 'WIDGET SETTINGS', 'thrive-cb' ); ?>
		</button>
	</div>
</div>
