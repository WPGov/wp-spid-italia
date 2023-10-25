jQuery( document ).ready( function ( $ ) {
	var body = $( 'body' ),
		toggleSSO = $( '#spid-toggle' ),
		userLogin = $( '#user_login' ),
		userPassword = $( '#user_pass' ),
		ssoWrap = $( '#spid-sso-wrap' ),
		loginForm = $( '#loginform' ),
		overflow = $( '<div class="spid-sso-clear"></div>' );

	
	// No action if page is rp (reset password)
	if ( body.hasClass( 'login-action-rp' ) ) {
		return;
	}

	// The overflow div is a poor man's clearfloat. We reposition the remember me
	// checkbox and the submit button within that to clear the float on the
	// remember me checkbox. This is important since we're positioning the SSO
	// UI under the submit button.
	//
	// @TODO: Remove this approach once core ticket 28528 is in and we have more actions in wp-login.php.
	// See - https://core.trac.wordpress.org/ticket/28528
	loginForm.append( overflow );
	overflow.append( $( 'p.forgetmenot' ), $( 'p.submit' ) );

	// We reposition the SSO UI at the bottom of the login form which
	// fixes a tab order issue. Then we override any styles for absolute
	// positioning of the SSO UI.
	loginForm.append( ssoWrap );
	body.addClass( 'spid-sso-repositioned' );

	toggleSSO.on( 'click', function ( e ) {
		e.preventDefault();
		body.toggleClass( 'spid-sso-form-display' );
		if ( ! body.hasClass( 'spid-sso-form-display' ) ) {
			userLogin.focus();
			userPassword.prop( 'disabled', false );
		}
	} );
} );