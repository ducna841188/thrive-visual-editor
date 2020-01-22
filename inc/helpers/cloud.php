<?php
/**
 * helper functions for cloud functionality
 */

/**
 * Backwards compatibility - replace tve_editor with the new global selector
 */
add_filter( 'tcb_alter_cloud_template_meta', 'tve_replace_cloud_template_global_selector', 10, 2 );

/**
 * Replace
 *
 * @param $data
 * @param $meta
 *
 * @return mixed
 */
function tve_replace_cloud_template_global_selector( $data, $meta ) {
	$data['head_css'] = tcb_custom_css( $data['head_css'] );

	return $data;
}

/**
 * Get cloud templates
 *
 * @param       $tag
 * @param array $args
 *
 * @return array|mixed|WP_Error
 */
function tve_get_cloud_content_templates( $tag, $args = array() ) {

	$args = wp_parse_args( $args, array(
		'nocache' => false,
	) );

	$do_not_use_cache = ( defined( 'TCB_TEMPLATE_DEBUG' ) && TCB_TEMPLATE_DEBUG ) || $args['nocache'];

	$transient = 'tcb_cloud_templates_' . $tag;

	if ( $do_not_use_cache || ! ( $templates = get_transient( $transient ) ) ) {

		require_once tve_editor_path( 'inc/classes/content-templates/class-tcb-content-templates-api.php' );

		try {
			$templates = tcb_content_templates_api()->get_all( $tag, $args );
			set_transient( $transient, $templates, 8 * HOUR_IN_SECONDS );
		} catch ( Exception $e ) {
			return new WP_Error( 'tcb_api_error', $e->getMessage() );
		}
	}

	return $templates;
}

/**
 * Get cloud templates data
 *
 * @param       $tag
 * @param array $args
 *
 * @return mixed
 */
function tve_get_cloud_template_data( $tag, $args = array() ) {

	if ( isset( $args['id'] ) ) {
		$id = $args['id'];
		unset( $args['id'] );
	}

	$args = wp_parse_args( $args, array(
		'nocache' => false,
	) );

	$force_fetch = ( defined( 'TCB_TEMPLATE_DEBUG' ) && TCB_TEMPLATE_DEBUG ) || $args['nocache'];

	require_once tve_editor_path( 'inc/classes/content-templates/class-tcb-content-templates-api.php' );
	$api = tcb_content_templates_api();

	/**
	 * check for newer versions - only download the template if there is a new version available
	 */
	$current_version = false;
	if ( ! $force_fetch ) {
		$all = apply_filters( 'tcb_filter_cloud_template_data', tve_get_cloud_content_templates( $tag ), $tag );

		if ( is_wp_error( $all ) ) {
			return $all;
		}

		foreach ( $all as $tpl ) {
			if ( isset( $id ) && $tpl['id'] == $id ) {
				$current_version = (int) ( isset( $tpl['v'] ) ? $tpl['v'] : 0 );
			}
		}
	}

	try {

		/**
		 * Download template if:
		 * $force_fetch OR
		 * template not downloaded OR
		 * template is downloaded but the version on the cloud has changed
		 */
		if ( $force_fetch || ! ( $data = $api->get_content_template( $id ) ) || ( $current_version !== false && $current_version > $data['v'] ) ) {
			$api->download( $id, $args );
			$data = $api->get_content_template( $id );
		}
	} catch ( Exception $e ) {
		$data = new WP_Error( 'tcb_download_err', $e->getMessage() );
	}

	return $data;
}

/**
 * get a list of templates from the cloud
 * search first in a local wp_option (to avoid making too many requests to the templates server)
 * cache the results for a set period of time
 *
 * default cache interval: 8h
 *
 * @return array
 */
function tve_get_cloud_templates() {
	$transient_name = apply_filters( 'tve_cloud_templates_transient_name', 'tcb_cloud_cache' );

	if ( defined( 'TCB_CLOUD_DEBUG' ) && TCB_CLOUD_DEBUG ) {
		delete_transient( $transient_name );
	}

	$cache_for = apply_filters( 'tcb_cloud_cache', 3600 * 8 );

	$templates = get_transient( $transient_name );
	if ( false === $templates ) {

		try {
			$templates = TCB_Landing_Page_Cloud_Templates_Api::getInstance()->getTemplateList();
			set_transient( $transient_name, $templates, $cache_for );
		} catch ( Exception $e ) {
			/* save the error message to display it in the LP modal */
			$GLOBALS['tcb_lp_cloud_error'] = $e->getMessage();
			$templates                     = array();
		}
	}

	return $templates;
}

/**
 * get the configuration stored in the wp_option table for this template (this only applies to templates downloaded from the cloud)
 * if $validate === true => also perform validations of the files (ensure the required files exist in the uploads folder)
 *
 * @param string $lp_template
 * @param bool   $validate if true, causes the configuration to be validated
 *
 * @return array|bool false in case there is something wrong (missing files, invalid template name etc)
 */
function tve_get_cloud_template_config( $lp_template, $validate = true ) {
	$templates = tve_get_downloaded_templates();
	if ( ! isset( $templates[ $lp_template ] ) ) {
		return false;
	}

	$config          = $templates[ $lp_template ];
	$config['cloud'] = true;

	/**
	 * skip the validation process if $validate is falsy
	 */
	if ( ! $validate ) {
		return $config;
	}

	$base_folder = tcb_get_cloud_base_path();

	$required_files = array(
		'templates/' . $lp_template . '.tpl', // html contents
		'templates/css/' . $lp_template . '.css', // css file
	);

	foreach ( $required_files as $file ) {
		if ( ! is_readable( $base_folder . $file ) ) {
			unset( $templates[ $lp_template ] );
			tve_save_downloaded_templates( $templates );

			return false;
		}
	}

	return $config;
}

/**
 * main entry-point for Landing Pages stored in the cloud - get all, download etc
 */
function tve_ajax_landing_page_cloud() {
	if ( empty( $_POST['task'] ) ) {
		$error = __( 'Invalid request', 'thrive-cb' );
	}

	if ( ! isset( $error ) ) {

		try {
			switch ( $_POST['task'] ) {
				case 'download':
					$template = isset( $_POST['template'] ) ? $_POST['template'] : '';
					$post_id  = isset( $_POST['post_id'] ) ? $_POST['post_id'] : '';
					if ( empty( $template ) ) {
						throw new Exception( __( 'Invalid template', 'thrive-cb' ) );
					}
					$force_download = defined( 'TCB_CLOUD_DEBUG' ) && TCB_CLOUD_DEBUG;
					if ( ! $force_download ) {
						$transient_name = 'tcb_template_download_' . $template;
						if ( get_transient( $transient_name ) === false ) {
							$force_download = true;
							set_transient( $transient_name, 1, DAY_IN_SECONDS );
						}
					}
					$downloaded = tve_get_downloaded_templates();

					if ( $force_download || ! array_key_exists( $template, $downloaded ) || tve_get_landing_page_config( $template ) === false ) {
						/**
						 * this will throw Exception if anything goes wrong
						 */
						TCB_Landing_Page_Cloud_Templates_Api::getInstance()->download( $template, 2 );
					}

					tcb_landing_page( $post_id )->set_cloud_template( $template );

					wp_send_json( array(
						'success' => true,
					) );
			}
		} catch ( Exception $e ) {
			wp_send_json( array(
				'success' => false,
				'message' => $e->getMessage(),
			) );
		}
	}

	wp_die();
}

/**
 * check if a landing page template is originating from the cloud (has been downloaded previously)
 *
 * @param string $lp_template
 *
 * @return bool
 */
function tve_is_cloud_template( $lp_template ) {
	if ( ! $lp_template ) {
		return false;
	}
	$templates = tve_get_downloaded_templates();

	/**
	 * Filter - allows modifying cloud template behaviour
	 *
	 * @param bool $is_cloud_template whether or not the current page has a cloud template applied
	 */
	return apply_filters( 'tcb_is_cloud_template', array_key_exists( $lp_template, $templates ) );
}
