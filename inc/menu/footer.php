<?php
/**
 * FileName  symbol.php.
 * @project: thrive-visual-editor
 * @developer: Dragos Petcu
 * @company: BitStone
 */
?>
<div id="tve-footer-component" class="tve-component" data-view="Footer">
	<div class="footer-select">
		<div class="dropdown-header" data-prop="docked">
			<?php echo __( 'Main Options', 'thrive-cb' ); ?>
			<i></i>
		</div>
		<div class="dropdown-content">
			<div class="pb-10 tcb-text-center grey-text">
				<?php echo __( 'This is a footer. You can edit it as a global element( it updates simultaneously in all places you used it ) or change it with another saved footer', 'thrive-tcb' ); ?>
			</div>
			<hr>
			<div class="row pb-10">
				<div class="col-xs-6">
					<button class="tve-button blue long click" data-fn="edit_footer">
						<?php echo __( 'Edit Footer', 'thrive-cb' ) ?>
					</button>
				</div>

				<div class="col-xs-6">
					<button class="tve-button grey long click" data-fn="placeholder_action">
						<?php echo __( 'Change Footer', 'thrive-cb' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="action-group" style="display: none">
		<div class="dropdown-header" data-prop="docked">
			<?php echo __( 'Footer Options', 'thrive-cb' ); ?>
			<i></i>
		</div>
		<div class="dropdown-content">
			<div class="tve-control" data-view="FooterWidth"></div>
			<hr>
			<div class="tve-control" data-view="FooterHeight"></div>
			<div class="tve-control" data-view="FooterFullHeight"></div>
			<hr>
			<div class="row">
				<div class="col-xs-12">
					<div class="tve-control" data-view="VerticalPosition"></div>
				</div>
			</div>
		</div>
	</div>
</div>
