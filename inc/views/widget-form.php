<span class="widget-display-name" style="display:none"><?php echo $data['widget']->name . ' ' . __( 'Options', 'thrive-cb' ); ?> </span>
<div class="widget-inside media-widget-control">
	<div class="form wp-core-ui">
		<input type="hidden" class="id_base" value="<?php echo esc_attr( $data['widget']->id_base ); ?>"/>
		<input type="hidden" class="widget-id" value="<?php echo uniqid( 'widget-', false ); ?>"/>
		<div class="widget-content">
			<?php $data['widget']->form( $data['form_data'] ); ?>
		</div>
	</div>
</div>
