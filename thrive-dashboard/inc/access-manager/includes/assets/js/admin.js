( function ( $ ) {
	var AM_View = Backbone.View.extend( {
		el: '.tvd-access-manager-setting',

		events: {
			'click .tvd-icon-no-cap': 'change_capability',
			'click .tvd-icon-cap': 'change_capability',
		},
		/**
		 * Get svg for the new icon
		 * @param name
		 * @returns {string}
		 */
		get_icon: function ( name ) {
			return '<svg class="tvd-icon tvd-icon-' + name + '"><use xlink:href="#icon-' + name + '"></use></svg>';
		},

		/**
		 * Ajax for changing a capability
		 * @param e
		 */
		change_capability: function ( e ) {
			const $target = $( e.target ).closest( '.tvd-am-cap' ),
				capability_action = $target.hasClass( 'tvd-am-with-cap' ) ? 'remove' : 'add',
				self = this;

			/**
			 * Disable change capability for dashboard if the user is admin
			 */
			if ( $target.attr( 'data-role' ) === 'administrator' && $target.attr( 'data-plugin' ) === 'tve-use-td' ) {
				TVE_Dash.err( TVE_Dash_Const.translations.CapabilityError );
				return;
			}

			TVE_Dash.showLoader();
			$.ajax( {
				url: ajaxurl,
				data: {
					action: TVE_Dash_Const.actions.backend_ajax,
					route: TVE_Dash_Const.routes.change_capability,
					role: $target.attr( 'data-role' ),
					capability: $target.attr( 'data-cap' ),
					capability_action: capability_action
				},
				type: 'post',
				dataType: 'json'
			} ).done( function ( response ) {

				if ( response.success ) {
					$target.html( capability_action === 'add' ? self.get_icon( 'cap' ) : self.get_icon( 'no-cap' ) );
					$target.toggleClass( 'tvd-am-without-cap', capability_action !== 'add' );
					$target.toggleClass( 'tvd-am-with-cap', capability_action === 'add' );
					TVE_Dash.success( TVE_Dash_Const.translations.CapabilitySuccess, 4000, null, 'top' );
				} else {
					TVE_Dash.err( response.message, 4000, null, 'top' );
				}
			} )
			 .always( function () {
				 TVE_Dash.hideLoader();
			 } );

		},
	} );
	var access_manager_view = new AM_View();
} )( jQuery );