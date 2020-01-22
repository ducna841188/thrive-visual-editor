<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

/**
 * Class TVD_Smart_DB
 */
class TVD_Smart_DB {
	/**
	 * Groups table name
	 *
	 * @var string
	 */
	private $groups_table_name;

	/**
	 * Fields table name
	 *
	 * @var string
	 */
	private $fields_table_name;

	/**
	 * Wordpress Database
	 *
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * Default fields and data
	 *
	 * @var array
	 */
	private $groups;

	/**
	 * Icons specific to type
	 */
	public static $icons;

	/**
	 * Types of fields
	 */
	public static $types
		= array(
			'text'    => 0,
			'address' => 1,
			'phone'   => 2,
			'email'   => 3,
			'link'    => 4,
			//			'location' => 5,
		);

	/**
	 * TVD_Smart_DB constructor.
	 */
	public function __construct() {
		global $wpdb;

		$this->wpdb              = $wpdb;
		$this->groups_table_name = $this->wpdb->prefix . 'td_groups';
		$this->fields_table_name = $this->wpdb->prefix . 'td_fields';
		$this->groups            = $this->groups();
		TVD_Smart_DB::$icons     = $this->icons();
	}

	/**
	 * Default icons
	 *
	 * return array
	 */
	public function icons() {
		return array(
			TVD_Smart_DB::$types['text']    => 'text-new',
			TVD_Smart_DB::$types['address'] => 'address-new',
			TVD_Smart_DB::$types['phone']   => 'phone-new',
			TVD_Smart_DB::$types['email']   => 'envelope-new',
			TVD_Smart_DB::$types['link']    => 'link-new',
			//			TVD_Smart_DB::$types['location'] => 'map-marker-solid',
		);
	}

	/**
	 * Default types
	 *
	 * return array
	 */
	public static function fieldTypes() {
		$types = array();
		foreach ( TVD_Smart_DB::$types as $key => $type ) {
			$types[ TVD_Smart_DB::$types[ $key ] ] = array(
				'name' => $key,
				'icon' => TVD_Smart_DB::field_icon( TVD_Smart_DB::$types[ $key ] ),
				'key'  => TVD_Smart_DB::$types[ $key ],
			);
		}

		return $types;
	}

	/**
	 * Default fields and data
	 *
	 * return array
	 */
	public function groups() {
		return array(
			'Company' => array(
				array(
					'name' => 'Name',
					'type' => TVD_Smart_DB::$types['text'],
				),
				array(
					'name' => 'Address',
					'type' => TVD_Smart_DB::$types['address'],
				),
				array(
					'name' => 'Phone number',
					'type' => TVD_Smart_DB::$types['phone'],
				),
				array(
					'name' => 'Alternative phone number',
					'type' => TVD_Smart_DB::$types['phone'],
				),
				array(
					'name' => 'Email address',
					'type' => TVD_Smart_DB::$types['email'],
				),
				//				array(
				//					'name' => 'Map Location',
				//					'type' => TVD_Smart_DB::$types['location'],
				//				),
			),
			'Legal'   => array(
				array(
					'name' => 'Privacy policy',
					'type' => TVD_Smart_DB::$types['link'],
					'data' => array( 'text' => 'Privacy policy', 'url' => '' ),
				),
				array(
					'name' => 'Disclaimer',
					'type' => TVD_Smart_DB::$types['link'],
					'data' => array( 'text' => 'Disclaimer', 'url' => '' ),
				),
				array(
					'name' => 'Terms and Conditions',
					'type' => TVD_Smart_DB::$types['link'],
					'data' => array( 'text' => 'Terms and Conditions', 'url' => '' ),
				),
				array(
					'name' => 'Contact',
					'type' => TVD_Smart_DB::$types['link'],
					'data' => array( 'text' => 'Contact', 'url' => '' ),
				),
			),
			'Social'  => array(
				array(
					'name' => 'Facebook Page',
					'icon' => 'facebook-brands',
					'type' => TVD_Smart_DB::$types['link'],
				),
				array(
					'name' => 'YouTube',
					'icon' => 'youtube-brands',
					'type' => TVD_Smart_DB::$types['link'],
				),
				array(
					'name' => 'LinkedIn',
					'icon' => 'linkedin-brands-new',
					'type' => TVD_Smart_DB::$types['link'],
				),
				array(
					'name' => 'Pinterest',
					'icon' => 'pinterest-brands-new',
					'type' => TVD_Smart_DB::$types['link'],
				),
				array(
					'name' => 'Instagram',
					'icon' => 'instagram-brands-new',
					'type' => TVD_Smart_DB::$types['link'],
				),
			),
		);
	}

	/**
	 * Insert the default data in the db
	 */
	public function insert_default_data() {
		/**
		 * We can't use the migration queries in the migration file because we have relationships, so we insert the data here
		 */
		$result = $this->wpdb->get_row( "SELECT `id` FROM $this->groups_table_name  LIMIT 0,1", ARRAY_A );

		if ( empty( $result ) ) {
			foreach ( $this->groups as $group => $fields ) {

				/**
				 * Insert the group
				 */
				$result = $this->wpdb->insert(
					$this->groups_table_name,
					array(
						'name'       => $group,
						'is_default' => 1,
					),
					array(
						'%s',
					)
				);
				$id     = $this->wpdb->insert_id;

				if ( $result ) {
					/**
					 * Insert the fields
					 */
					foreach ( $fields as $field ) {
						$this->wpdb->insert(
							$this->fields_table_name,
							array(
								'name'       => $field['name'],
								'type'       => $field['type'],
								'data'       => empty( $field['data'] ) ? null : maybe_serialize( $field['data'] ),
								'is_default' => 1,
								'group_id'   => $id,
							),
							array(
								'%s',
								'%d',
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Get groups with fields
	 *
	 * @param int $id
	 *
	 * @return array|object|null
	 */
	public function get_groups( $id = 0, $with_fields = true ) {
		$args  = array();
		$query = 'SELECT * FROM ' . $this->groups_table_name;
		if ( $id ) {
			$where  = ' WHERE id = %d';
			$args[] = $id;
		} else {
			/**
			 * We need this so WPDB won't complain about not preparing the data correctly
			 */
			$where  = ' WHERE 1 = %d';
			$args[] = 1;
		}

		$query .= $where;

		$results = $this->wpdb->get_results( $this->wpdb->prepare( $query, $args ), ARRAY_A );

		if ( $results && $with_fields ) {

			foreach ( $results as $key => $group ) {
				$results[ $key ]['fields']        = $this->get_fields( $group );
				$results[ $key ]['default_field'] = empty( $this->groups[ $results[ $key ]['name'] ] ) ? 0 : 1;
				if ( ! empty( $results[ $key ]['fields'] ) ) {
					foreach ( $results[ $key ]['fields'] as $_key => $field ) {
						$field['group_name']                                 = $group['name'];
						$results[ $key ]['fields'][ $_key ]['formated_data'] = empty( $field['data'] ) ? '' : TVD_Smart_DB::format_field_data( maybe_unserialize( $field['data'] ), $field );
						$results[ $key ]['fields'][ $_key ]['data']          = empty( $field['data'] ) ? '' : maybe_unserialize( $field['data'] );

						$results[ $key ]['fields'][ $_key ]['icon']          = empty( $this->groups[ $results[ $key ]['name'] ][ $_key ]['icon'] ) ? TVD_Smart_DB::field_icon( $field['type'] ) : dashboard_icon( $this->groups[ $results[ $key ]['name'] ][ $_key ]['icon'], true );
						$results[ $key ]['fields'][ $_key ]['default_field'] = empty( $this->groups[ $results[ $key ]['name'] ][ $_key ] ) ? 0 : 1;
					}
				}
			}
		}

		return $results;
	}

	public static function field_icon( $field_type ) {
		return dashboard_icon( TVD_Smart_DB::$icons[ $field_type ], true );
	}

	public static function format_field_data( $field_data, $field, $args = array() ) {
		$data        = '';
		$unavailable = '';
		if ( apply_filters( 'td_smartsite_shortcode_tooltip', false ) ) {
			$unavailable_name = empty( $field['group_name'] ) ? $field['name'] : '[' . $field['group_name'] . '] ' . $field['name'];
			$unavailable      = '<span class="thrive-inline-shortcode-unavailable">' .
			                    '<span class="thrive-shortcode-notice">' .
			                    '!' .
			                    '</span>' .
			                    $unavailable_name .
			                    '</span>' .
			                    '<span class="thrive-tooltip-wrapper">' .
			                    '<span class="thrive-shortcode-tooltip">' .
			                    __( 'This global variable hasn\'t been set.  Define your global variables in the', TVE_DASH_TRANSLATE_DOMAIN ) .
			                    '<br>' .
			                    '<a><span onClick=window.open("' . add_query_arg( 'page', 'tve_dash_smart_site', admin_url( 'admin.php' ) ) . '","_blank") >' . ' ' . __( ' Global Fields dashboard', TVE_DASH_TRANSLATE_DOMAIN ) . '</span></a>' .
			                    '</span>' .
			                    '</span>';
		}

		switch ( (int) $field['type'] ) {
			// text field
			case TVD_Smart_DB::$types['text']:
				$data = empty( $field_data['text'] ) ? $unavailable : $field_data['text'];
				break;
			//address field
			case TVD_Smart_DB::$types['address']:
				if ( empty( $field_data['address1'] ) ) {
					$data = $unavailable;
				} else {
					$data = implode( empty( $args['multiline'] ) ? ', ' : '<br>', array_filter( $field_data ) );
				}
				break;
			// phone field
			case TVD_Smart_DB::$types['phone']:
				$data = empty( $field_data['phone'] ) ? $unavailable : $field_data['phone'];
				break;
			// email field
			case TVD_Smart_DB::$types['email']:
				$data = empty( $field_data['email'] ) ? $unavailable : $field_data['email'];
				break;
			//link field
			case TVD_Smart_DB::$types['link']:
				$data = empty( $field_data['url'] ) ? $unavailable : '<a href="' . $field_data['url'] . '" target="_blank">' . $field_data['text'] . '</a>';
				break;
			// location field
			case TVD_Smart_DB::$types['location']:
				$url = 'https://maps.google.com/maps?q=' . urlencode( empty( $field_data['location'] ) ? 'New York' : $field_data['location'] ) . '&t=m&z=10&output=embed&iwloc=near';

				$data = '<iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="' . $url . '"></iframe>';
				break;
		}

		return $data;
	}

	/**
	 * Get fields for group or by ID
	 *
	 * @param array $group
	 * @param int   $id
	 *
	 * @return array|object|null
	 */
	public function get_fields( $group = array(), $id = 0 ) {
		if ( $group ) {
			$where  = ' WHERE group_id = %d';
			$args[] = $group['id'];
		} else {
			/**
			 * We need this so WPDB won't complain about not preparing the data correctly
			 */
			$where  = ' WHERE 1 = %d';
			$args[] = 1;
		}

		if ( $id ) {
			$where  .= ' AND id = %d';
			$args[] = $id;
		}

		$query = $this->wpdb->prepare( 'SELECT * FROM ' . $this->fields_table_name . $where, $args );

		if ( ! $id ) {
			$results = $this->wpdb->get_results( $query, ARRAY_A );
		} else {
			$results = $this->wpdb->get_row( $query, ARRAY_A );
		}


		return $results;
	}

	/**
	 * Get fields of a specific type which have some data
	 *
	 * @param array $groupId
	 * @param int   $type
	 *
	 * @return array|object|null
	 */
	public function getFieldsByType( $groupId = array(), $type = 0 ) {
		if ( $type ) {
			$where  = ' WHERE type = %d';
			$args[] = $type;
		} else {
			return array();
		}

		if ( $groupId ) {
			$where  .= ' AND group_id = %d';
			$args[] = $groupId;
		} else {
			$where  .= ' AND 1 = %d';
			$args[] = 1;
		}

		$query = $this->wpdb->prepare( 'SELECT * FROM ' . $this->fields_table_name . $where, $args );

		return $this->wpdb->get_results( $query, ARRAY_A );
	}

	public static function save_field( $model, $action ) {
		$rep = array(
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);

		global $wpdb;

		// Add new field
		if ( $action === 'insert' ) {
			$model['created_at'] = date( 'Y-m-d h:i:s' );
			$result              = $wpdb->insert(
				$wpdb->prefix . 'td_fields',
				array(
					'group_id'   => $model['group_id'],
					'name'       => $model['name'],
					'type'       => $model['type'],
					'data'       => maybe_serialize( $model['data'] ),
					'created_at' => $model['created_at'],
				),
				$rep
			);
			$model['id']         = (string) $wpdb->insert_id;
		} else {
			// Update existing field
			$model['updated_at'] = date( 'Y-m-d h:i:s' );
			$result              = $wpdb->update(
				$wpdb->prefix . 'td_fields',
				array(
					'group_id'   => (int) $model['group_id'],
					'name'       => $model['name'],
					'type'       => $model['type'],
					'data'       => maybe_serialize( $model['data'] ),
					'updated_at' => $model['updated_at'],
				),
				array( 'id' => $model['id'] ),
				$rep,
				array( '%d' )
			);
		}

		$model['formated_data'] = TVD_Smart_DB::format_field_data( $model['data'], $model );
		$model['icon']          = empty( $model['icon'] ) ? TVD_Smart_DB::field_icon( $model['type'] ) : $model['icon'];

		return $result ? $model : false;
	}

	public static function delete_field( $id ) {
		global $wpdb;

		return $wpdb->delete( $wpdb->prefix . 'td_fields', array( 'id' => $id ) );
	}

	public static function insert_group( $model ) {

		$model['created_at'] = date( 'Y-m-d h:i:s' );

		global $wpdb;

		$result = $wpdb->insert(
			$wpdb->prefix . 'td_groups',
			array(
				'name'       => $model['name'],
				'created_at' => $model['created_at'],
			),
			array(
				'%s',
				'%s',
			)
		);

		$model['id'] = (string) $wpdb->insert_id;

		return $result ? $model : false;
	}

	public static function update_group( $model ) {

		global $wpdb;
		$model['updated_at'] = date( 'Y-m-d h:i:s' );

		$result = $wpdb->update(
			$wpdb->prefix . 'td_groups',
			array(
				'name'       => $model['name'],
				'updated_at' => $model['updated_at'],
			),
			array( 'id' => $model['id'] ),
			array( '%s', '%s', ),
			array( '%d' )
		);

		return $result ? $model : false;
	}

	public static function delete_group( $id ) {
		global $wpdb;

		$result = $wpdb->delete( $wpdb->prefix . 'td_groups', array( 'id' => $id ) );
		if ( $result ) {
			$wpdb->delete( $wpdb->prefix . 'td_fields', array( 'group_id' => $id ), array( '%d' ) );
		}

		return $result;
	}
}