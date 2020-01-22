<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

$data_attr = '';

foreach ( TCB_Post_List::default_args() as $key => $value ) {
	$data_attr .= 'data-' . $key . '="' . $value . '" ';
}

?>

<main class="tcb-post-list tcb-compact-element thrv_wrapper" <?php echo $data_attr; ?> data-element-name="Post List" draggable="true" data-css="tve-u-16b3297f569">
	<article class="<?php echo TCB_POST_WRAPPER_CLASS; ?>">
		<a class="tcb-post-thumbnail tcb-shortcode thrv_wrapper tve-draggable tve-droppable tcb-selector-no_save tcb-post-list-shortcode" href="#" title="blank post 5" data-type-url="post_url" data-type-display="default_image" data-css="" data-size="full" data-post_id="" draggable="true" data-shortcode="tcb_post_featured_image"></a>
		<div class="tcb-clear tcb-post-list-cb-clear">
			<div class="thrv_wrapper thrv_contentbox_shortcode thrv-content-box tcb-post-list-cb tve-draggable tve-droppable" draggable="true">
				<div class="tve-content-box-background"></div>
				<div class="tve-cb">
					<p class="tcb-post-categories tcb-shortcode thrv_wrapper tve-draggable tve-droppable tcb-selector-no_save tcb-post-list-shortcode" data-link="1" draggable="true" data-shortcode="tcb_post_categories"></p>
				</div>
			</div>
		</div>

		<h2 class="tcb-post-title tcb-shortcode thrv_wrapper tve-draggable tve-droppable tcb-selector-no_save tcb-post-list-shortcode" draggable="true" data-shortcode="tcb_post_title"></h2>

		<section class="tcb-post-content tcb-shortcode thrv_wrapper tve-draggable tve-droppable tcb-selector-no_save tcb-post-list-shortcode" data-size="words" data-read_more="" data-words="12" draggable="true" data-shortcode="tcb_post_content"><p><a href="#" draggable="false"></a></p></section>

		<div class="tcb-clear tcb-post-read-more-clear">
			<div class="tcb-post-read-more thrv_wrapper tve-draggable tve-droppable" draggable="true">
				<a href="#" class="tcb-button-link tcb-post-read-more-link" draggable="false">
					<span class="tcb-button-texts">
						<span class="tcb-button-text thrv-inline-text"><?php echo __( '​Read More', 'thrive-cb' ); ?></span>
					</span>
				</a>
			</div>
		</div>
	</article>
</main>
