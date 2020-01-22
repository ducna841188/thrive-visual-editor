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
<div class="thrv_wrapper thrv_lead_generation tve-draggable tve-droppable edit_mode active_delete" data-connection="api"><input type="hidden" class="tve-lg-err-msg" value="{&quot;email&quot;:&quot;Email address invalid&quot;,&quot;phone&quot;:&quot;Phone number invalid&quot;,&quot;password&quot;:&quot;Password invalid&quot;,&quot;passwordmismatch&quot;:&quot;Password mismatch error&quot;,&quot;required&quot;:&quot;Required field missing&quot;}">
	<div class="thrv_lead_generation_container tve_clearfix">
		<form action="#" method="post" novalidate="">
			<div class="tve_lead_generated_inputs_container tve_clearfix">
				<div class="tve_lg_input_container tve_lg_input">
					<input type="text" data-field="name" name="name" placeholder="Name" data-placeholder="Name">
				</div>
				<div class="tve_lg_input_container tve_lg_input">
					<input type="email" data-field="email" data-required="1" data-validation="email" name="email" placeholder="Email" data-placeholder="Email">
				</div>
				<div class="tve_lg_input_container tve_submit_container tve_lg_submit">
					<button type="submit"><?php echo __( 'Sign Up', 'thrive-cb' ); ?></button>
				</div>
			</div>
			<input id="_submit_option" type="hidden" name="_submit_option" value="redirect">
			<input id="_back_url" type="hidden" name="_back_url" value="#">
		</form>
	</div>
</div>
