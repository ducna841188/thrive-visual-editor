<div class="error-container"></div>
<div class="tve-modal-content">
	<div id="cb-cloud-menu">
		<div class="fixed top">
			<div class="lp-search">
				<input type="text" data-source="search" class="keydown" data-fn="filter" placeholder="<?php echo esc_html__( 'Search', 'thrive-cb' ); ?>"/>
				<?php tcb_icon( 'search-regular' ); ?>
				<?php tcb_icon( 'close2', false, 'sidebar', 'click', array( 'data-fn' => 'domClearSearch' ) ); ?>
			</div>
		</div>
		<div class="lp-menu-wrapper fixed">
			<div class="lp-label-wrapper">
				<span><?php echo __( 'CATEGORY', 'thrive-cb' ); ?></span>
				<span class="separator"></span>
			</div>
			<div id="lp-blk-pack-categories"></div>
			<div class="lp-label-wrapper mt-30">
				<span><?php echo __( 'Block types', 'thrive-cb' ); ?></span>
				<span class="separator"></span>
			</div>
			<div id="lp-groups-wrapper"></div>
		</div>
		<div class="fixed bottom">
			<div class="switch" data-tooltip="<?php echo esc_html__( 'If this option is activated, the page block will inherit the colors of your smart landing page, when you insert it to the page', 'thrive-cb' ); ?>" data-side="top">
				<span class="fill"><?php echo __( 'Match Landing Page Colors', 'thrive-cb' ); ?></span>
				<div class="tcb-switch">
					<label>
						<input class="tve-checkbox change" data-fn="domMatchLpColors" type="checkbox">
						<span class="tcb-lever"></span>
					</label>
				</div>
			</div>
		</div>
	</div>
	<div id="cb-cloud-templates">
		<div id="lp-blk-pack-title" class="mb-5"></div>
		<div id="lp-blk-pack-description"></div>
		<div id="cb-pack-content"></div>
	</div>
</div>