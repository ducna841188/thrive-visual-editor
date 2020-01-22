<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package TCB2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

?>
<div id="tve-toc-component" class="tve-component" data-view="TOC">
	<div class="dropdown-header" data-prop="docked">
		<?php echo __( 'Main Options', 'thrive-cb' ); ?>
		<i></i>
	</div>
	<div class="dropdown-content">
		<div class="hide-states">
			<div class="tve-control mb-10 tve-toc-control" data-view="Headings"></div>
			<hr>
			<div class="tve-control" data-view="HeaderColor"></div>
			<div class="tve-control" data-view="HeadBackground"></div>
			<div class="tve-control" data-view="Columns"></div>
			<div class="tve-control" data-view="Evenly"></div>
		</div>
	</div>
</div>
