<?php
/*
Plugin Name: WP SPID Italia
Description: SPID - Sistema Pubblico di Identità Digitale
Author: Marco Milesi
Version: 2.12
Author URI: http://www.marcomilesi.com
*/

include( plugin_dir_path( __FILE__ ) . 'constants.php');
include( plugin_dir_path( __FILE__ ) . 'frontend-ui.php');
include( plugin_dir_path( __FILE__ ) . 'user.php');

add_action( 'admin_menu', function() {
    add_submenu_page(
        'options-general.php',
        'SPID', 'SPID',
        'manage_options', 'spid_menu',
        function() { include( plugin_dir_path( __FILE__ ) . 'admin/settings.php'); spid_menu_func(); }
    );
} );

add_action( 'admin_init', function() {
    register_setting('spid_options', 'spid');
    $arrayatpv = get_plugin_data ( __FILE__ );
    $nuova_versione = $arrayatpv['Version'];
    if ( version_compare( get_option('spid_version'), $nuova_versione, '<' )) {
      update_option( 'spid_version', $nuova_versione );
    }
});

add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), function( $links ) {
    $settings_link = '<a href="options-general.php?page=spid_menu">Impostazioni</a>';
    array_push( $links, $settings_link );
  	return $links;
} );

add_filter('wp_login_errors', function($errors) {
    if ( isset($_GET['SimpleSAML_Auth_State_exceptionId']) ) {
        $errors->add('access', 'Login SPID non riuscito. Riprova tra qualche istante.');
    } else if ( isset($_GET['spid']) && $_GET['spid'] ) {
        $errors->add('access', 'Non è stata trovata alcuna utenza associata all\'indirizzo email o codice fiscale di SPID');
    }
    return $errors;
} );
  
add_action( 'init', function() {
    
    if ( session_status() == PHP_SESSION_NONE ) {
        session_start();
    }
    
    if ( isset( $_GET['spid_metadata'] ) && $_GET['spid_metadata'] == spid_get_metadata_token()  ) {
	    header( 'Content-type: text/xml' );
        $sp = spid_load();
        echo $sp->getSPMetadata( isset( $_GET['type'] ) ? sanitize_title( $_GET['type'] ) : NULL );
        die();
    }
} );

add_shortcode( 'spid_login_button', function( $atts ) {
    $a = shortcode_atts( array(
		'size' => 's',
		'redirect_to' => '',
	), $atts );

    // Sanitize all input
    $size = sanitize_text_field($a['size']);
    $redirect_to = sanitize_text_field($a['redirect_to']);
	
    // Handle the 'CURRENT_URL' placeholder safely
    if ($redirect_to === 'CURRENT_URL') {
        global $wp;
        $redirect_to = home_url( $wp->request );
    }

    // Escape attributes for output
    $button = spid_get_login_button(esc_attr($size), esc_url($redirect_to));
    return $button;
} );

add_action( 'login_form', function() {

    if ( !is_spid_enabled() ) {
        return;
    }

    $site_name = get_bloginfo( 'name' );
		if ( ! $site_name ) {
			$site_name = get_bloginfo( 'url' );
		}

		$display_name = ! empty( $_COOKIE[ 'spid_sso_wpcom_name_' . COOKIEHASH ] )
			? $_COOKIE[ 'spid_sso_wpcom_name_' . COOKIEHASH ]
			: false;
		$gravatar = ! empty( $_COOKIE[ 'spid_sso_wpcom_gravatar_' . COOKIEHASH ] )
			? $_COOKIE[ 'spid_sso_wpcom_gravatar_' . COOKIEHASH ]
			: false;
		?>
		<div id="spid-sso-wrap">
			<?php

				if ( $display_name && $gravatar ) : ?>
				<div id="spid-sso-wrap__user">
					<img width="72" height="72" src="<?php echo esc_html( $gravatar ); ?>" />

					<h2>
						<?php
							echo wp_kses(
								sprintf( __( 'Log in as <span>%s</span>', 'spid' ), esc_html( $display_name ) ),
								array( 'span' => true )
							);
						?>
					</h2>
				</div>

			<?php endif; ?>

        <div id="spid-sso-wrap__action">
            <p>
                <div class="spid-sso-wrap__inner">
                    <?php echo spid_get_idp_list(); ?>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
                <div class="spid-sso-or"><span><?php esc_html_e( apply_filters( 'spid_filter_login_or_after', __( 'Oppure', 'spid' ) ) ); ?></span></div>
            </p>
        </div>

        <div class="spid-sso-or spid-sso-toggle default">
            <span><?php esc_html_e( apply_filters( 'spid_filter_login_or_pre', __( 'Oppure', 'spid' ) ) ); ?></span>
        </div>
        
        <a href="<?php echo esc_url( add_query_arg( 'spid-sso-show-default-form', '1' ) ); ?>" class="spid-sso-toggle wpcom">
            <?php esc_html_e( apply_filters( 'spid_filter_loginbutton_footer', __( 'Log in with username and password', 'spid' ) ) ); ?>
        </a>
        <div class="spid-sso-toggle default">
            <?php echo spid_get_loginform_button(); ?>
        </div>
    </div>
<?php
} );

add_filter( 'logout_url', function( $logout_url ) {
    try {
        $sp = spid_load();
        if ( $sp && $sp->isAuthenticated() ) {
            return wp_spid_italia_get_login_url( 'out' ) .'?spid_sso=out';
        }
    } catch ( Exception  $e) {
	    
    }
    return $logout_url;
}, 10, 2 );

function spid_get_metadata_url( $type = NULL ) {
    $url = '';
    switch ( $type ) {
        case 'aggregator':
            $url = add_query_arg( 'spid_metadata', spid_get_metadata_token(), trailingslashit( get_home_url() ) );
            $url = add_query_arg( 'type', sanitize_text_field( $type ), $url );
            break;
        default:
            $url = add_query_arg( 'spid_metadata', spid_get_metadata_token(), trailingslashit( get_home_url() ) );
            break;
    }
    return $url;
}

function spid_get_metadata_token() {
    $token = get_option( 'spid_metadata_token');
    if ( !$token ) {
        update_option( 'spid_metadata_token', substr(str_shuffle(str_repeat($x='0123456789-abcdefghijklmnopqrstuvwxyz', ceil( 15 / strlen($x)) )), 1, 15 ) );
        $token = get_option( 'spid_metadata_token');
    }
    return $token;
}

add_action('init', function() {
    if ( isset( $_GET['spid_sso'] ) && $_GET['spid_sso'] == 'in' ) {
        spid_handle();
    } else if ( isset( $_GET['spid_sso'] ) && $_GET['spid_sso'] == 'out' ) {
        spid_handle();
    }
} );

add_filter( 'login_message', function( $message ) { spid_handle(); });

function spid_handle() {
    $internal_debug = false;
    $spid_debug = ( WP_DEBUG === true ) || $internal_debug;

    if ( $internal_debug ) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    try {
        $sp = spid_load();
        if ( $sp ) {
            $sp->isAuthenticated();
        }

    } catch ( Exception  $e) {

        if ( $internal_debug ) {
            echo '<br><br><pre><small style="color:darkred;">'.$e->getMessage().'</small></pre>';
        } else {
            #echo '<br><br><pre><small style="color:darkred;">La configurazione SPID ha generato un errore</small></pre>';
        }

        add_filter( 'login_errors', 'spid_errors' );
        return;
    }

    if ( $internal_debug ) {
        echo '<div class="login"><form>';
        echo '<b>SPID Debug</b><br>';
        echo '<small>';
        echo '<br>Auth state: '.( $sp->isAuthenticated() ? 'authenticated' : 'not authenticated' );
        echo '<br>idpEntityId: '. ( isset( $_SESSION['idpEntityId'] ) ? $_SESSION['idpEntityId'] : '(not set)' );
        $xmlString = isset($_GET['SAMLResponse']) ? gzinflate(base64_decode($_GET['SAMLResponse'])) : ( isset($_POST['SAMLResponse']) ? base64_decode($_POST['SAMLResponse']) : '');
        if ( $xmlString ) {
            $xmlResp = new \DOMDocument();
            $xmlResp->loadXML($xmlString);
            echo '<br>SAMLResponse: '. $xmlString;
        }
        echo '<br>Session: ';
        print_r( $_SESSION );
        echo '</small>';
        echo '</form></div>';
    }

    if ( isset( $_GET['spid_sso'] ) && $_GET['spid_sso'] == 'out' ) {
        wp_clear_auth_cookie();
        remove_action('login_footer', 'wp_shake_js', 12);
        add_filter( 'login_errors', function() { return 'Disconnesso da SPID'; } );
        $sp->logoutPost( 0, wp_spid_italia_get_login_url( 'out' ) .'?spid_sso=out' );
    } else if (isset($_POST) && isset($_POST['selected_idp'])) {
        $idp = $_POST['selected_idp'];
    } else if ( isset( $_GET['spid_sso'] ) && $_GET['spid_sso'] == 'in' ) {
        
        if ( is_user_logged_in() ) {
            wp_logout();
        }

        if ( isset( $_GET['spid_idp'] ) && $_GET['spid_idp'] != '' ) {
            if ( $sp->isAuthenticated() ) {
                session_destroy();
		        $_SESSION = NULL;
                session_start();
            }
            if ( isset( $_GET['spid_redirect_to'] ) ) {
                $_SESSION['spid_redirect_to'] = $_GET['spid_redirect_to'];
            }
            $assertId = 0; // index of assertion consumer service as per the SP metadata (sp_assertionconsumerservice in settings array)
            $attrId = 0; // index of attribute consuming service as per the SP metadata (sp_attributeconsumingservice in settings array)
            $_SESSION['start_login'] = 1;
            //print_r($_SESSION);
            //die();
            $sp->login( 'idp_'.$_GET['spid_idp'], $assertId, $attrId ); // Generate the login URL and redirect to the IdP login page
        } else if ( $sp->isAuthenticated() ) {
            $attributes = $sp->getAttributes();
            $name = $attributes['email'][0];    
            $user = get_user_by( 'email', $attributes['email'] );
            $cf = str_replace( 'TINIT-', '', $attributes['fiscalNumber']);

            if ( empty( $user ) ) { // If user do not exists, look up by fiscal code
                $users = get_users(
                    array(
                        'meta_key' => 'codice_fiscale',
                        'meta_value' => $cf,
                        'number' => 1,
                        'count_total' => false,
                    )
                );
                if ( !empty( $users ) ) {
                    $user = reset( $users );
                } else {
                    $user = apply_filters( 'spid_registration_filter_new_user', $attributes );
                }
            }
            if ( is_a( $user, 'WP_User' ) && !is_wp_error( $user ) && !empty( $user ) ) {

                apply_filters( 'spid_registration_filter_existing_user', $attributes, $user );
                
                spid_update_user( $user, $attributes );

                wp_clear_auth_cookie();
                wp_set_current_user ( $user->ID );
                wp_set_auth_cookie  ( $user->ID );
            
                $redirect_to = (isset($_SESSION['spid_redirect_to']) && !empty($_SESSION['spid_redirect_to'])) ? $_SESSION['spid_redirect_to'] : admin_url();
                wp_safe_redirect( apply_filters( 'spid_registration_default_login_redirect', $redirect_to ) );
                exit();

            } else {
                remove_action('login_footer', 'wp_shake_js', 12);
                add_filter( 'login_errors', function() {
                    return 'Il tuo account non è abilitato su questo sito.';
                 } );    
            }
        } else {
            remove_action('login_footer', 'wp_shake_js', 12);
            add_filter( 'login_errors', function() { return 'SPID - Riprovare'; } );
        }

    }
}

function spid_errors( $errorMsg2 ){
    $xmlString = isset($_GET['SAMLResponse']) ? gzinflate(base64_decode($_GET['SAMLResponse'])) : base64_decode($_POST['SAMLResponse']);
    $xmlResp = new \DOMDocument();
    $xmlResp->loadXML($xmlString);
    if ( $xmlResp->textContent ) {
        switch ( $xmlResp->textContent ) {
            case stripos( $xmlResp->textContent, 'nr19') !== false:
                return '<b>SPID errore 19</b> - Ripetuta sottomissione di credenziali errate';
            case stripos( $xmlResp->textContent, 'nr20') !== false:
                return '<b>SPID errore 20</b> - Utente privo di credenziali compatibili con il livello richiesto dal fornitore del servizio';
            case stripos( $xmlResp->textContent, 'nr21') !== false:
                return '<b>SPID errore 21</b> - Timeout';
            case stripos( $xmlResp->textContent, 'nr22') !== false:
                return '<b>SPID errore 22</b> - Utente nega il consenso all\'invio di dati al SP in caso di sessione vigente';
            case stripos( $xmlResp->textContent, 'nr23') !== false:
                return '<b>SPID errore 23</b> - Credenziali sospese o revocate';
            case stripos( $xmlResp->textContent, 'nr25') !== false:
                return '<b>SPID errore 25</b> - Processo di autenticazione annullato dall\'utente';
            default:
                return 'Si è verificato un errore durante l\'accesso SPID. Contattare l\'amministratore per maggiori informazioni.';
        }
    }
}

function spid_update_user( $user, $attributes ) {
    
    $cf = str_replace( 'TINIT-', '', $attributes['fiscalNumber']);

    $first_name = '';
    $last_name = '';
    
    if ( isset( $attributes['name'] ) ) {
        $first_name = ucwords( strtolower( $attributes['name'] ) );
        update_user_meta( $user->ID, 'first_name', ucwords( strtolower( $attributes['name'] ) ) );
    }
    if ( isset( $attributes['familyName'] ) ) {
        $last_name = ucwords( strtolower( $attributes['familyName'] ) );
        update_user_meta( $user->ID, 'last_name', $last_name );
    }

    if ( $first_name && $last_name ) {
        wp_update_user( array( 'ID' => $user->ID, 'display_name' => $first_name.' '.$last_name ) );
    }
    
    update_user_meta( $user->ID, 'spid_attributes', $attributes);
    update_user_meta( $user->ID, 'codice_fiscale', $cf);

    return;
}
 
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style( 'spid-css', plugins_url( 'css/spid-sp-access-button.min.css', __FILE__ ), false );
    wp_enqueue_script( 'spid-js-button', plugins_url( 'js/spid-sp-access-button.min.js', __FILE__ ), array( 'jquery' )  );
} );

add_action( 'login_enqueue_scripts', function() {
    if ( isset($_GET['action']) && $_GET['action'] == 'lostpassword' ) {
        return;
    }
    wp_enqueue_style( 'spid-css', plugins_url( 'css/spid-sp-access-button.min.css', __FILE__ ), false );
    wp_enqueue_script( 'spid-js-button', plugins_url( 'js/spid-sp-access-button.min.js', __FILE__ ), array( 'jquery' )  );
    wp_enqueue_script( 'spid-js-loginform', plugins_url( 'js/spid-sp-loginform.js', __FILE__ ), array( 'jquery' )  );
}, 1 );

function wp_spid_italia_get_login_url( $dir = 'default' ) {
    $default_url = wp_login_url();

    $filter_default = apply_filters( 'spid_filter_login_url_dir_default', $default_url );
    $filter_in = apply_filters( 'spid_filter_login_url_dir_in', $default_url );
    $filter_out = apply_filters( 'spid_filter_login_url_dir_out', $default_url );

    if ( $dir == 'default' ) {
        return $filter_default;
    }

    if ( $dir == 'in' && $filter_in != $default_url ) {
        return $filter_in;
    } else if ( $dir == 'out' && $filter_out != $default_url ) {
        return $filter_out;
    }
    
    return $filter_default;
}

function is_spid_enabled() {
    return spid_option('enabled');
}

function spid_load() {
    
    if ( !is_spid_enabled() ) {
        return false;
    }

    if ( !is_dir( SPID__PERM_DIR ) ) {
        mkdir( SPID__PERM_DIR );
    }

    if ( !is_dir( SPID__CERT_DIR ) ) {
        mkdir( SPID__CERT_DIR );
    }

    require_once( SPID__LIB_DIR . 'vendor/autoload.php' );

    // ["name", "fiscalNumber", "email", "spidCode", "familyName", "placeOfBirth", "countyOfBirth", "dateOfBirth", "gender", "mobilePhone", "address"]

    return new Italia\Spid\Sp(
        array(
            'sp_entityid' => get_site_url(),
            'sp_key_file' => SPID__CERT_DIR.'sp.key',
            'sp_cert_file' => SPID__CERT_DIR.'sp.crt',
            'sp_comparison' => 'minimum', // one of: "exact", "minimum", "better" or "maximum"
            'sp_assertionconsumerservice' => [
                add_query_arg( 'spid_sso', 'in', wp_spid_italia_get_login_url( 'in' ) ) // Servizio standard
            ],
			'sp_singlelogoutservice' => [ [ add_query_arg( 'spid_sso', 'out', wp_spid_italia_get_login_url( 'out' ) ), '' ] ],
            'sp_org_name' => spid_option( 'sp_org_name' ),
            'sp_org_display_name' => spid_option( 'sp_org_display_name' ),
            'sp_contact_ipa_code' => spid_option( 'sp_contact_ipa_code' ),
            'sp_contact_email' => spid_option( 'sp_contact_email' ),
            'sp_contact_phone' => spid_option( 'sp_contact_phone' ),
            'sp_key_cert_values' => [ // Optional: remove this if you want to generate .key & .crt files manually
                'countryName' => spid_option( 'countryName' ),
                'stateOrProvinceName' => spid_option( 'stateOrProvinceName' ),
                'localityName' => spid_option( 'localityName' ),
                'commonName' => spid_option( 'commonName' ),
                'emailAddress' => spid_option( 'emailAddress' ),
            ],
            'idp_metadata_folder' => plugin_dir_path( __FILE__ ) . 'metadata/',
            'sp_attributeconsumingservice' => [ apply_filters( 'spid_filter_sp_attributeconsumingservice', [ "name", "familyName", "fiscalNumber", "email" ] ) ]
        ), null, true
    );
}

function spid_option($name) {
	$options = get_option('spid');
	if (isset($options[$name])) {
		return $options[$name];
	}
	return false;
}

?>
