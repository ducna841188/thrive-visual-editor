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
<div class="thrv_wrapper thrv-contact-form" data-ct="contact_form-21969">
	<form action="" method="post" novalidate>
		<div class="tve-cf-item-wrapper">
			<div class="tve-cf-item">
				<div class="thrv-cf-input-wrapper" data-type="first_name">
					<label><?php echo __( 'First Name', 'thrive-cb' ) ?></label>
					<div class="tve-cf-input">
						<input placeholder="John" data-placeholder="John" type="text" name="first_name" required="required">
					</div>
				</div>
			</div>
			<div class="tve-cf-item">
				<div class="thrv-cf-input-wrapper" data-type="email">
					<label><?php echo __( 'Email Address', 'thrive-cb' ) ?></label>
					<div class="tve-cf-input">
						<input placeholder="j.doe@inbox.com" data-placeholder="j.doe@inbox.com" type="email" name="email" required="required">
					</div>
				</div>
			</div>
			<div class="tve-cf-item">
				<div class="thrv-cf-input-wrapper" data-type="message">
					<label><?php echo __( 'Message', 'thrive-cb' ) ?></label>
					<div class="tve-cf-input">
						<textarea placeholder="Type your message here..." data-placeholder="Type your message here..." name="message" required="required"></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="tve-cf-submit">
			<button class="tve_btn_txt" type="submit"><?php echo __( 'Send message', 'thrive-cb' ) ?></button>
		</div>
	</form>
</div>
