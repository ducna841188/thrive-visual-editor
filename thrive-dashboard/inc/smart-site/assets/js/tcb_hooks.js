var TVD_SS = TVD_SS || {};

( function ( $ ) {


	if ( typeof TVE !== 'undefined' ) {
		TVE.add_filter( 'tcb.inline_shortcodes.insert', tvd_shape_shortcode );
	}

	/**
	 * Before shortcode insertion we do some processing of the data which will later shape the shortcode element ( shortcodeData structure can be seen bellow )
	 *
	 * @param shortcodeData
	 * @returns {*}
	 */
	//	shortcodeData = {
	//		key: shortcode_key,
	//		extra_key: shortcode_extra_key,
	//		name: name,
	//      shortcodeName: name,
	//		class: SHORTCODE_CLASS,
	//		content_class: SHORTCODE_CONTENT_CLASS,
	//	    configOptions: [        )
	//			{                   )
	//				key: '',        )        used for inputs that require further configuration
	//				value: '',      )        these will generate inputs inside the froala shortcode dropdown
	//			}                   )
	//		]                       )
	//		options: [                  ]
	//			{                       ]
	//				key: '',            ]   used for additional information passed  through the shortcode itself
	//				value: '',          ]   these don't do much but will b part of the final shortcode structure
	//			}                       ]
	//		]                           ]
	//	};
	function tvd_shape_shortcode( shortcodeData ) {
		var shortcode, name, shortcodeName;

		_.each( TVE.CONST.inline_shortcodes, function ( group, group_name ) {
			if ( ! shortcode ) {
				shortcode = group.find( function ( item ) {
					return shortcodeData.extra_key && item.extra_param === shortcodeData.extra_key;
				} );
			}
		} );
		if ( shortcode ) {

			shortcodeName = shortcode.input.id.value[ shortcodeData.configOptions.find( function ( item ) {
				return item.key === 'id';
			} ).value ];

			if ( shortcodeName ) {
				shortcodeData.shortcodeName = '[' + shortcodeData.shortcodeName + '] ' + shortcodeName;
				shortcodeData.name = shortcodeData.shortcodeName;
			}

			name = shortcode.input.id.real_data[ shortcodeData.configOptions.find( function ( item ) {
				return item.key === 'id';
			} ).value ];

			if ( name ) {
				shortcodeData.name = name;
			}

			var multiline = shortcodeData.configOptions.find( function ( item ) { return item.key === 'multiline'; } );
			if ( multiline && multiline.value ) {
				shortcodeData.name = shortcodeData.name.split(',').join('<br/>');
			}
		}

		return shortcodeData;
	}
} )( jQuery );