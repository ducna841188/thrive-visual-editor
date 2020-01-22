<?php
/**
 * Thrive Themes - https://thrivethemes.com
 */

/**
 * Handle Email connection for Lead Generation.
 * This connection will always be available but won't be displayed in TD, because no action is needed for it
 *
 * Class Thrive_Dash_List_Connection_Email
 */
class Thrive_Dash_List_Connection_Email extends Thrive_Dash_List_Connection_Abstract {

	/**
	 * @return string the API connection title
	 */
	public function getTitle() {
		return 'Email';
	}

	/**
	 * Remove the api from dashboard
	 *
	 * @return bool
	 */
	public function isRelated() {
		return true;
	}

	/**
	 * Email connection will always be available
	 *
	 * @return bool
	 */
	public function isConnected() {
		return true;
	}

	/**
	 * Noting to do here
	 */
	public function outputSetupForm() {
	}

	/**
	 * @return true
	 */
	public function readCredentials() {

		$this->setCredentials( array( 'connected' => true ) );
		$this->save();

		return true;
	}

	/**
	 * @return bool
	 */
	public function testConnection() {
		return true;
	}

	/**
	 * Send the emails on lg submit, the name may be a bit inappropriate, but we have to stay with the general implementation
	 *
	 * @param array $list_identifier
	 * @param array $arguments
	 *
	 * @return array|string
	 */
	public function addSubscriber( $list_identifier, $arguments ) {

		if ( ! is_array( $list_identifier ) ) {
			return __( 'Failed to send email', TVE_DASH_TRANSLATE_DOMAIN );
		}

		$response = array();

		foreach ( $list_identifier as $connection ) {

			$_list = $connection['list'];

			if ( 'own_site' === $_list ) {
				$_list = 'email';
			}

			$_instance = Thrive_Dash_List_Manager::connectionInstance( $_list );

			if ( ! method_exists( $_instance, 'sendMultipleEmails' ) ) {
				continue;
			}

			$connection = array_merge( $connection, $arguments );

			$response[ $_instance->getKey() ] = $_instance->sendMultipleEmails( $this->prepare_data_for_email_service( $connection ) );
		}

		return $response;
	}

	protected function _apiInstance() {
	}

	protected function _getLists() {
		return $this->get_connected_email_providers();
	}

	/**
	 * Get connected email providers
	 *
	 * @return array
	 */
	public function get_connected_email_providers() {

		$providers = array(
			array(
				'id'   => 'own_site',
				'name' => 'Send emails from this site',
			),
		);

		foreach ( Thrive_Dash_List_Manager::getAvailableAPIsByType( true, array( 'email' ) ) as $email_provider ) {

			/**
			 * @var Thrive_Dash_List_Connection_Abstract $email_provider
			 */
			$providers[] = array(
				'id'   => $email_provider->getKey(),
				'name' => $email_provider->getTitle(),
			);
		}

		return $providers;
	}

	/**
	 * @param array $arguments
	 *
	 * @return bool
	 */
	public function sendMultipleEmails( $arguments ) {
		$headers = 'Content-Type: text/html; charset=UTF-8 ' . "\r\n"
		           . 'From: ' . $arguments['from_name'] . ' <' . $arguments['from_email'] . ' > ' . "\r\n"
		           . 'Reply-To: ' . $arguments['reply_to'] . "\r\n"
		           . 'CC: ' . implode( ', ', $arguments['cc'] ) . "\r\n"
		           . 'BCC: ' . implode( ', ', $arguments['bcc'] ) . "\r\n";

		return wp_mail(
			$arguments['emails'],
			$arguments['subject'],
			$arguments['html_content'],
			$headers
		);
	}

	/**
	 * Get any extra settings needed by the api
	 *
	 * @param array $arguments
	 *
	 * @return array
	 */
	public function get_extra_settings( $arguments = array() ) {

		$response = array();

		foreach ( Thrive_Dash_List_Manager::getAvailableAPIsByType( true, array( 'email' ) ) as $email_provider ) {

			/**
			 * @var Thrive_Dash_List_Connection_Abstract $email_provider
			 */
			$response[ $email_provider->getKey() ] = array(
				'from_email' => $email_provider->get_email_param(),
			);
		}

		return $response;
	}

	/**
	 * Prepare data for email service
	 *
	 * @param array $arguments
	 *
	 * @return array
	 */
	public function prepare_data_for_email_service( $arguments ) {

		$emails = array_map(
			function ( $item ) {
				return sanitize_email( trim( $item ) );
			},
			explode( ',', $arguments['to'] )
		);

		$cc  = array();
		$bcc = array();

		if ( ! empty( $arguments['cc'] ) ) {
			$cc = array_map(
				function ( $item ) {
					return sanitize_email( trim( $item ) );
				},
				explode( ',', $arguments['cc'] )
			);
		}

		if ( ! empty( $arguments['bcc'] ) ) {
			$bcc = array_map(
				function ( $item ) {
					return sanitize_email( trim( $item ) );
				},
				explode( ',', $arguments['bcc'] )
			);
		}

		return array(
			'emails'       => $emails,
			'subject'      => sanitize_text_field( $arguments['email_subject'] ),
			'from_name'    => sanitize_text_field( $arguments['from_name'] ),
			'from_email'   => sanitize_email( $arguments['from_email'] ),
			'html_content' => $this->get_email_html( $arguments ),
			'reply_to'     => sanitize_email( $arguments['reply_to'] ),
			'bcc'          => $bcc,
			'cc'           => $cc,
		);
	}

	/**
	 * Get email html based on provided args
	 *
	 * @param array $args
	 *
	 * @return false|string
	 */
	protected function get_email_html( $args ) {
		$timezone      = get_option( 'gmt_offset' );
		$time          = date( 'H:i', time() + 3600 * ( $timezone + date( 'I' ) ) );
		$has_shortcode = strpos( $args['email_message'], '[ form_fields ]' );
		$chunks        = explode( '[ form_fields ]', $args['email_message'] );

		$before = '';
		$after  = '';

		if ( isset( $chunks[0] ) ) {
			$before = preg_replace( "/[\r\n]+/", "\n", $chunks[0] ); //replace multiple newlines with only one
			$before = nl2br( $before );
		}

		if ( isset( $chunks[1] ) ) {
			$after = preg_replace( "/[\r\n]+/", "\n", $chunks[1] ); //replace multiple newlines with only one
			$after = nl2br( $after );
		}

		ob_start();

		include dirname( dirname( dirname( __FILE__ ) ) ) . '/views/includes/email.php';

		$html = ob_get_clean();

		$html .= $this->generate_custom_fields_html( $args );

		return $before . $html . $after;
	}

	/**
	 * Get all custom fields from request args
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private function _get_custom_fields( $args ) {
		$mapping         = unserialize( base64_decode( $args['tve_mapping'] ) );
		$apis            = Thrive_Dash_List_Manager::getAvailableAPIsByType( true, array( 'email', 'other' ) );
		$custom_fields   = array();
		$excluded_fields = array( 'name', 'email', 'phone' );

		foreach ( $apis as $api ) {
			/** @var Thrive_Dash_List_Connection_Abstract $api */

			$cf = $api->get_custom_fields();
			$cf = wp_list_pluck( $cf, 'id' );

			$custom_fields = array_merge( $custom_fields, $cf );
		}

		$custom_fields = array_unique( $custom_fields );
		$custom_fields = array_filter(
			$custom_fields,
			function ( $field ) use ( $excluded_fields, $args ) {
				if ( ! in_array( $field, $excluded_fields ) && array_key_exists( $field, $args ) ) {
					return $field;
				}
			}
		);
		$custom_fields = array_merge( $custom_fields, array_keys( $mapping ) );

		return $custom_fields;
	}

	/**
	 * Generate the html for custom fields added in lg
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function generate_custom_fields_html( $args ) {

		$html   = '';
		$labels = ! empty( $args['tve_labels'] ) ? unserialize( base64_decode( $args['tve_labels'] ) ) : array();

		foreach ( $this->_get_custom_fields( $args ) as $field ) {
			$label = ! empty( $labels[ $field ] ) ? sanitize_text_field( $labels[ $field ] ) : __( 'Extra Data', TVE_DASH_TRANSLATE_DOMAIN );
			$value = ! empty( $args[ $field ] ) ? sanitize_text_field( $args[ $field ] ) : '';

			if ( 'password' === $field || 'confirm_password' === $field ) {
				$value = '******';
			}

			$_html = '<b>' . $label . ':</b> <span>' . $value . '</span><br>';

			$html .= $_html;
		}

		return $html;
	}
}
