<?php
/**
 * FileName  header.php.
 *
 * @project: thrive-visual-editor
 * @company: BitStone
 */
?>
<div id="tve-header-component" class="tve-component" data-view="Header">
	<div class="header-select">
		<div class="dropdown-header" data-prop="docked">
			<?php echo __( 'Main Options', 'thrive-cb' ); ?>
			<i></i>
		</div>
		<div class="dropdown-content">
			<div class="pb-10 tcb-text-center">
				<?php echo __( 'This is a header. You can edit it as a global element( it updates simultaneously in all places you used it) or change it with another saved header', 'thrive-tcb' ); ?>
			</div>
			<hr>
			<div class="row pb-10">
				<div class="col-xs-6">
					<button class="tve-button blue long click" data-fn="edit_header">
						<?php echo __( 'Edit Header', 'thrive-cb' ) ?>
					</button>
				</div>

				<div class="col-xs-6">
					<button class="tve-button grey long click" data-fn="placeholder_action">
						<?php echo __( 'Change Header', 'thrive-cb' ) ?>
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="action-group" style="display: none">
		<div class="dropdown-header" data-prop="docked">
			<?php echo __( 'Header Options', 'thrive-cb' ); ?>
			<i></i>
		</div>
		<div class="dropdown-content">
			<div class="tve-control" data-view="HeaderPosition"></div>
			<hr>
			<div class="tve-control" data-view="HeaderWidth"></div>
			<hr>
			<div class="tve-control" data-view="HeaderHeight"></div>
			<div class="tve-control" data-view="HeaderFullHeight"></div>
			<hr>
			<div class="tve-control" data-view="VerticalPosition"></div>
		</div>
	</div>

</div>
