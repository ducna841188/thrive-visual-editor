<div id="sidebar-bottom">
	<div class="center-content">
		<a href="javascript:void(0)" class="click selected" data-fn="change_preview" data-device="desktop">
			<?php tcb_icon( 'desktop-regular' ); ?>
		</a>
		<span class="sep"></span>
		<a href="javascript:void(0)" class="click" data-fn="change_preview" data-device="tablet">
			<?php tcb_icon( 'tablet-android-light' ); ?>
		</a>
		<span class="sep"></span>
		<a href="javascript:void(0)" class="click" data-fn="change_preview" data-device="mobile">
			<?php tcb_icon( 'mobile-light' ); ?>
		</a>
		<a href="<?php echo tcb_get_preview_url(); ?>" target="_blank" class="preview-content tvd-fixed-right" data-width="fluid" data-tooltip="<?php echo __( 'Preview Saved Version', 'thrive-cb' ); ?>" data-side="top">
			<span><?php echo __( 'Preview', 'thrive-cb' ); ?></span>
		</a>
	</div>
</div>
