<div class="control-grid no-space wrap">
	<label for="v-yt-url"><?php echo __( 'URL', 'thrive-cb' ) ?></label>
	<input type="text" data-setting="url" class="yt-url fill" id="v-yt-url">
</div>
<div class="yt-url-validate inline-message"></div>

<div class="extra-settings">
	<div class="inline-checkboxes">
		<label class="tcb-checkbox"><input type="checkbox" data-setting="a" value="1" checked="checked"><span><?php echo __( 'Autoplay', 'thrive-cb' ) ?></span></label>
		<?php if( 1 === 2 ): ?>
		<label class="tcb-checkbox"><input type="checkbox" data-setting="htb" value="1"><span><?php echo __( 'Hide title', 'thrive-cb' ) ?></span></label>
		<?php endif;?>
		<label class="tcb-checkbox"><input type="checkbox" data-setting="hyl" value="1"><span><?php echo __( 'Hide logo', 'thrive-cb' ) ?></span></label>
		<label class="tcb-checkbox"><input type="checkbox" data-setting="hrv" value="1"><span><?php echo __( 'Optimize related', 'thrive-cb' ) ?></span></label>
		<label class="tcb-checkbox"><input type="checkbox" data-setting="ahi" value="1"><span><?php echo __( 'Hide controls', 'thrive-cb' ) ?></span></label>
		<label class="tcb-checkbox"><input type="checkbox" data-setting="hfs" value="1"><span><?php echo __( 'Hide full-screen', 'thrive-cb' ) ?></span></label>
	</div>
</div>