<div class="tcb-modals">
	<?php foreach ( $data['files'] as $file ) : $modal_id = 'tcb-modal-' . str_replace( array( '.php', '_' ), array( '', '-' ), basename( $file ) ); ?>
		<div id="<?php echo esc_attr( $modal_id ); ?>" class="tcb-modal <?php echo esc_attr( $modal_id ); ?>">
			<div class="tcb-modal-content">
				<?php include $file; ?>
			</div>
			<span class="click tcb-modal-close"><?php tcb_icon( 'close2' ); ?></span>
		</div>
	<?php endforeach; ?>
</div>
