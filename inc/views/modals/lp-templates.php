<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
?>
<div style="position: absolute;top: 0;" id="lp-lightbox-drop-panels"></div>
<h2 class="tcb-modal-title">
	<?php if ( defined( 'TVE_STAGING_TEMPLATES' ) && TVE_STAGING_TEMPLATES ) : ?>
		<span style="color: #810000"><?php echo __( 'Warning! The templates listed here are only used for testing purposes', 'thrive-cb' ); ?></span>
	<?php else : ?>
		<?php echo __( 'Choose Landing Page Template', 'thrive-cb' ); ?>
	<?php endif ?>
</h2>
<?php if ( ! empty( $GLOBALS['tcb_lp_cloud_error'] ) ) : ?>
	<?php $support_link = '<a href="https://thrivethemes.com/forums/forum/plugins/thrive-architect/" title="Support Forum">' . __( 'Support Forum', 'thrive-cb' ) . '</a>' ?>
	<div class="cloud-lp-error message-inline">
		<div class="tcb-notification">
			<div class="tcb-notification-icon tcb-notification-icon-error">
				<?php tcb_icon( 'close2' ) ?>
			</div>
			<div class="tcb-notification-content">
				<div>
					<?php echo sprintf( __( 'An error was encountered while fetching Cloud Landing Page Templates. Please contact our %s and provide the following error message:', 'thrive-cb' ), $support_link ) ?>
					<pre style="color: #e74c3c"><?php echo esc_html( $GLOBALS['tcb_lp_cloud_error'] ) ?></pre>
				</div>
			</div>
		</div>
	</div>
<?php else : ?>
	<div class="mt-20">
		<?php echo __( 'Any changes you’ve made to the current landing page will be lost when you select a new template. We recommend you to save your current template first.', 'thrive-cb' ) ?>
	</div>
<?php endif ?>

<div class="tve-templates-wrapper">
	<div class="tve-header-tabs">
		<div class="tab-item active" data-content="default"><?php echo __( 'Default Templates', 'thrive-cb' ); ?></div>
		<div class="tab-item" data-content="saved"><?php echo __( 'Saved Landing Pages', 'thrive-cb' ); ?></div>
		<div class="tags-filter">
			<div class="tags-title">
				<?php echo __( 'Filter templates by tags', 'thrive-cb' ); ?>
				<?php tcb_icon( 'a_down' ); ?>
			</div>
			<div class="tags-select">
				<input class="tags-search" placeholder="<?php echo __( 'Search for tags', 'thrive-cb' ); ?>">
				<div class="template-tags"></div>
			</div>
		</div>
	</div>
	<div class="tve-tabs-content" style="overflow-y: scroll">
		<div class="tve-tab-content active" data-content="default">
			<?php if ( apply_filters( 'tcb_show_smart_landing_pages', true ) ) : ?>
				<div class="mb-10 collapsible-header">
					<span class="new-tag"><?php echo __( 'New', 'thrive-cb' ) ?></span>
					<span class="pl-10" style="font-size: 23px;"><?php echo __( 'Smart Landing Pages', 'thrive-cb' ) ?></span>
					<span class="hr-divider"></span>
					<a class="toggle-lp-type" data-lp-type="smart" href="javascript:void(0)"><?php tcb_icon( 'chevron-down-regular' ); ?></a>
				</div>
				<div class="lp-type-section" data-lp-type="smart">
					<div class="mb-20">
						<?php echo __( 'Landing Pages loaded with smart features like easy colors management, group editing, global fields and much more.', 'thrive-cb' ); ?> <a class="learn_more_link" href="https://thrivethemes.com/tkb_item/what-are-smart-landing-pages-and-how-to-use-them/" target="_blank"><u><?php echo __( 'Learn more', 'thrive-cb' ); ?></u> <?php tcb_icon( 'info' ); ?></a>
					</div>
					<div class="tve-smart-templates-list tve-templates-list"></div>
				</div>
				<div class="mb-10 collapsible-header">
					<span class="pl-10" style="font-size: 23px;"><?php echo __( 'Legacy Landing Pages', 'thrive-cb' ) ?></span>
					<span class="hr-divider"></span>
					<a class="toggle-lp-type" data-lp-type="legacy" href="javascript:void(0)"><?php tcb_icon( 'chevron-down-regular' ); ?></a>
				</div>
			<?php endif; ?>
			<div class="tve-default-templates-list tve-templates-list lp-type-section" data-lp-type="legacy"></div>
		</div>
		<div class="tve-tab-content" data-content="saved">
			<div class="tve-saved-templates-list expanded-set"></div>
		</div>
		<div class="tve-template-preview"></div>
	</div>
</div>

<div class="tcb-modal-footer clearfix pt-15 row end-xs">
	<div class="col col-xs-12">
		<button type="button" class="tcb-right tve-button medium green tcb-modal-save tcb-disabled">
			<?php echo __( 'Choose Template', 'thrive-cb' ) ?>
		</button>
	</div>
</div>

