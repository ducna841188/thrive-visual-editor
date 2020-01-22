<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}
?>
<div class="status tpl-ajax-status"><?php echo __( 'Fetching data', 'thrive-cb' ); ?>...</div>

<h2 class="tcb-modal-title"><?php echo __( 'Choose', 'thrive-cb' ); ?> <span class="element-name"></span></h2>
<div class="tve-templates-wrapper">
	<div id="cloud-templates" class="content-templates tve-templates-container pb-10"></div>
</div>
<div class="tcb-absolute flex space-between" style="left: 40px;right:40px;bottom:20px">
	<button type="button" class="tcb-left tve-button medium gray tcb-modal-cancel"><?php echo __( 'Cancel', 'thrive-cb' ); ?></button>
	<button type="button" class="tcb-right tve-button medium green tcb-modal-save"><?php echo __( 'Choose Divider', 'thrive-cb' ); ?></button>
</div>
