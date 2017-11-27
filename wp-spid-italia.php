<?php
/*
Plugin Name: WP SPID Italia
Description: SPID - Sistema Pubblico di Identità Digitale
Author: Marco Milesi
Version: 1.1
Author URI: http://www.marcomilesi.ml
*/

include( plugin_dir_path( __FILE__ ) . 'constants.php');

register_activation_hook( __FILE__, function(){

    $dir = array(
        SPID__PERM_DIR,
        SPID__CONFIG_DIR,
        SPID__CERT_DIR,
        SPID__TEMP_DIR,
        SPID__DATA_DIR,
        SPID__LOG_DIR
    );

    foreach ($dir as $value) {
        if ( !is_dir( $value ) && !mkdir( $value, 0755, false)) {
            die('Errore durante la creazione della directory <b>'.$value.'</b>!<br>
            Verificare che la directory sia scrivibile, provare a riattivare il plugin o crearla manualmente.');
        }
    }

    if ( !file_exists( SPID__PERM_DIR . '/.htaccess' ) && !copy( SPID__LIB_DIR . '/.htaccess', SPID__PERM_DIR . '/.htaccess') ) {
        die('Cannot write .htaccess...');
    }

    if ( !file_exists( SPID__CONFIG_DIR . '/config.php' ) && !copy( SPID__LIB_DIR . '/config-templates-pasw/config.php', SPID__CONFIG_DIR . '/config.php') ) {
        die('Cannot write config.php...');
    }

    if ( !file_exists( SPID__CONFIG_DIR . '/authsources.php' ) && !copy( SPID__LIB_DIR . '/config-templates-pasw/authsources.php', SPID__CONFIG_DIR . '/authsources.php') ) {
        die('Cannot write authsources.php...');
    }
});

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
    if ( version_compare( get_option('spid_version'), $nuova_versione, '<')) {
      update_option( 'spid_version', $nuova_versione );
    }
});

include( plugin_dir_path( __FILE__ ) . 'user.php');

/*
add_action('wp_logout', function() {
    require_once( SPID__LIB_DIR . '/lib/_autoload.php');

    $_simplesamlphp_auth_saml_config = SimpleSAML_Configuration::getInstance();
    $auth = new SimpleSAML_Auth_Simple( 'default-sp' );
    if ( $auth->isAuthenticated() ) {
        $auth->logout(array(
            'ReturnTo' => get_site_url(),
            'ReturnStateParam' => 'LogoutState',
            'ReturnStateStage' => 'MyLogoutState'));
    }
}, 10, 1);

add_filter( 'logout_url', function( $logout_url, $redirect ) {
    require_once( SPID__LIB_DIR . '/lib/_autoload.php');
    $_simplesamlphp_auth_saml_config = SimpleSAML_Configuration::getInstance();
    $auth = new SimpleSAML_Auth_Simple( 'default-sp' );
    if ( $auth->isAuthenticated() ) {
        return $auth->getLogoutURL( get_site_url() );
    }
    return $logout_url;
}, 10, 2 );
*/
add_filter( 'logout_url', function( $logout_url, $redirect ) {
    
    if ( !is_file (SPID__CERT_DIR.'/saml.pem') || !spid_option('enabled') ) {
        return $logout_url;
    }
    
    require_once( SPID__LIB_DIR . '/lib/_autoload.php');
    
    $auth = new SimpleSAML_Auth_Simple( 'default-sp' );
    if ( $auth->isAuthenticated() ) {
        if ( $redirect ) {
            $red = $redirect;
        } else {
            $red = get_site_url();
        }
        return $auth->getLogoutURL( $logout_url );
    }
    return $logout_url;
}, 10, 2 );

add_filter( 'plugin_action_links_'.plugin_basename( __FILE__ ), function( $links ) {
    $settings_link = '<a href="options-general.php?page=spid_menu">Impostazioni</a>';
    array_push( $links, $settings_link );
  	return $links;
} );

add_filter( 'login_message', function( $message ) {

    if ( !is_file (SPID__CERT_DIR.'/saml.pem') || !spid_option('enabled') ) {
        return;
    }
    
    if ( isset($_GET['SimpleSAML_Auth_State_exceptionId']) ) {
        echo '<div id="login_error"><b>ERRORE</b>: login SPID non riuscito. Riprova tra qualche istante.</div>';
    }
    require_once( SPID__LIB_DIR . '/lib/_autoload.php');

    $auth = new SimpleSAML_Auth_Simple( 'default-sp' );
    $_simplesamlphp_auth_saml_attributes = $auth->getAttributes();

    if ( isset( $_GET['saml_login'] ) && $_GET['saml_login'] == 'spid' ) {
        if ((isset($_REQUEST['infocert_id']) && $_REQUEST['infocert_id'])) {
            $options['saml:idp'] = $_REQUEST['infocert_id'];
        } elseif ((isset($_REQUEST['poste_id']) && $_REQUEST['poste_id'])) {
            $options['saml:idp'] = $_REQUEST['poste_id'];
        } elseif ((isset($_REQUEST['tim_id']) && $_REQUEST['tim_id'])) {
            $options['saml:idp'] = $_REQUEST['tim_id'];
        } elseif ((isset($_REQUEST['sielte_id']) && $_REQUEST['sielte_id'])) {
            $options['saml:idp'] = $_REQUEST['sielte_id'];
        } elseif ((isset($_REQUEST['aruba_id']) && $_REQUEST['aruba_id'])) {
            $options['saml:idp'] = $_REQUEST['aruba_id'];
        } elseif ((isset($_REQUEST['namirial_id']) && $_REQUEST['namirial_id'])) {
            $options['saml:idp'] = $_REQUEST['namirial_id'];
        } elseif ((isset($_REQUEST['register_id']) && $_REQUEST['register_id'])) {
            $options['saml:idp'] = $_REQUEST['register_id'];
        } else {
            echo '<b>ERRORE</b>';
        }
    
        if ( is_user_logged_in() ) {
            wp_logout();
        }

        $authformat = 'https://www.spid.gov.it/%s';
        $authlevel = 'SpidL1';
        $options['saml:AuthnContextClassRef'] = sprintf($authformat, $authlevel);
        $options['samlp:RequestedAuthnContext'] = array("Comparison" => "minimum");
        $options['ErrorURL'] = wp_login_url();
        $auth->requireAuth( $options );
    }

    if ( $auth->isAuthenticated() ) {
        $attributes = $auth->getAttributes();
        $name = $attributes['email'][0];    
        $user = get_user_by( 'email', $attributes['email'][0] );
        $cf = str_replace( 'TINIT-', '', $attributes['fiscalNumber'][0]);
        
        if ( empty( $user ) ) {
            $user = reset(
                get_users(
                 array(
                  'meta_key' => 'codice_fiscale',
                  'meta_value' => $cf,
                  'number' => 1,
                  'count_total' => false,
                 )
                )
            );
        }

        if ( !is_wp_error( $user ) && !empty( $user ) ) {
            update_user_meta( $user->ID, 'spid_attributes', $attributes);
            update_user_meta( $user->ID, 'codice_fiscale', $cf);
            wp_clear_auth_cookie();
            wp_set_current_user ( $user->ID );
            wp_set_auth_cookie  ( $user->ID );
        
            wp_safe_redirect( user_admin_url() );
            exit();
        } else {
            $auth->logout( get_home_url() );
            exit();
        }

    }

    $plugin_dir = plugin_dir_url( __FILE__ );
    $spid_ico_circle_svg = $plugin_dir . '/img/spid-ico-circle-bb.svg';
    $spid_ico_circle_png = $plugin_dir . '/img/spid-ico-circle-bb.png';

    $spid_idp_infocert_svg =  $plugin_dir . '/img/spid-idp-infocertid.svg';
    $spid_idp_infocert_png = $plugin_dir . '/img/spid-idp-infocertid.png';

    $spid_idp_timid_svg = $plugin_dir . '/img/spid-idp-timid.svg';
    $spid_idp_timid_png = $plugin_dir . '/img/spid-idp-timid.png';

    $spid_idp_posteid_svg = $plugin_dir . '/img/spid-idp-posteid.svg';
    $spid_idp_posteid_png = $plugin_dir . '/img/spid-idp-posteid.png';

    $spid_idp_sielteid_svg = $plugin_dir . '/img/spid-idp-sielteid.svg';
    $spid_idp_sielteid_png = $plugin_dir . '/img/spid-idp-sielteid.png';

    $spid_idp_arubaid_svg = $plugin_dir . '/img/spid-idp-arubaid.svg';
    $spid_idp_arubaid_png = $plugin_dir . '/img/spid-idp-arubaid.png';

    $spid_idp_namirialid_svg = $plugin_dir . '/img/spid-idp-namirialid.svg';
    $spid_idp_namirialid_png = $plugin_dir . '/img/spid-idp-namirialid.png';

    $spid_idp_registerid_svg = $plugin_dir . '/img/spid-idp-spiditalia.svg';
    $spid_idp_registerid_png = $plugin_dir . '/img/spid-idp-spiditalia.png';

    $infocert_id = 'https://identity.infocert.it';
    $poste_id = 'https://posteid.poste.it';
    $tim_id = 'https://login.id.tim.it/affwebservices/public/saml2sso';
    $sielte_id = 'https://identity.sieltecloud.it';
    $aruba_id = 'https://loginspid.aruba.it';
	$namirial_id = 'https://idp.namirialtsp.com/idp';
	$register_id = 'https://spid.register.it';

  $formaction = $auth->getLoginURL();
    ?>
    
    <form name="spid_idp_access" action="?saml_login=spid" method="post" style="text-align: center;background:none;box-shadow:none;margin: 10px 0;padding: 0;">
            <a href="#" class="italia-it-button italia-it-button-size-s button-spid" spid-idp-button="#spid-idp-button-small-post" aria-haspopup="true" aria-expanded="false">
                <span class="italia-it-button-icon"><img src="<?php echo $spid_ico_circle_svg; ?>" onerror="this.src='<?php echo $spid_ico_circle_png; ?>'; this.onerror=null;" alt="" /></span>
                <span class="italia-it-button-text">Entra con SPID</span>
            </a>
            <div id="spid-idp-button-small-post" class="spid-idp-button spid-idp-button-tip spid-idp-button-relative">
                <ul id="spid-idp-list-small-root-post" class="spid-idp-button-menu" aria-labelledby="spid-idp">
                    <li class="spid-idp-button-link">
                        <button class="idp-button-idp-logo" name="infocert_id" type="submit" value="<?php echo $infocert_id; ?>"><span class="spid-sr-only">Infocert ID</span><img class="spid-idp-button-logo" src="<?php echo $spid_idp_infocert_svg; ?>" onerror="this.src='<?php echo $spid_idp_infocert_png; ?>'; this.onerror=null;" alt="Infocert ID" /></button>
                    </li>
                    <li class="spid-idp-button-link">
                        <button class="idp-button-idp-logo" name="poste_id" type="submit" value="<?php echo $poste_id; ?>"><span class="spid-sr-only">Poste ID</span><img class="spid-idp-button-logo" src="<?php echo $spid_idp_posteid_svg; ?>" onerror="this.src='<?php echo $spid_idp_posteid_png; ?>'; this.onerror=null;" alt="Poste ID" /></button>
                    </li>
                    <li class="spid-idp-button-link">
                        <button class="idp-button-idp-logo" name="tim_id" type="submit" value="<?php echo $tim_id; ?>"><span class="spid-sr-only">Tim ID</span><img class="spid-idp-button-logo" src="<?php echo $spid_idp_timid_png; ?>" onerror="this.src='<?php echo $spid_idp_timid_svg; ?>'; this.onerror=null;" alt="Tim ID" /></button>
                    </li>
                    <li class="spid-idp-button-link">
                        <button class="idp-button-idp-logo" name="sielte_id" type="submit" value="<?php echo $sielte_id; ?>"><span class="spid-sr-only">Sielte ID</span><img class="spid-idp-button-logo" src="<?php echo $spid_idp_sielteid_png; ?>" onerror="this.src='<?php echo $spid_idp_sielteid_svg; ?>'; this.onerror=null;" alt="Sielte ID" /></button>
                    </li>
                    <li class="spid-idp-button-link">
                        <button class="idp-button-idp-logo" name="aruba_id" type="submit" value="<?php echo $aruba_id; ?>"><span class="spid-sr-only">Aruba ID</span><img class="spid-idp-button-logo" src="<?php echo $spid_idp_arubaid_png; ?>" onerror="this.src='<?php echo $spid_idp_arubaid_svg; ?>'; this.onerror=null;" alt="Aruba ID" /></button>
                    </li>
                    <li class="spid-idp-button-link">
                        <button class="idp-button-idp-logo" name="namirial_id" type="submit" value="<?php echo $namirial_id; ?>"><span class="spid-sr-only">Namirial ID</span><img class="spid-idp-button-logo" src="<?php echo $spid_idp_namirialid_png; ?>" onerror="this.src='<?php echo $spid_idp_namirialid_svg; ?>'; this.onerror=null;" alt="Namirial ID" /></button>
                    </li>
                    <li class="spid-idp-button-link">
                        <button class="idp-button-idp-logo" name="register_id" type="submit" value="<?php echo $register_id; ?>"><span class="spid-sr-only">SpidItalia ID</span><img class="spid-idp-button-logo" src="<?php echo $spid_idp_registerid_png; ?>" onerror="this.src='<?php echo $spid_idp_registerid_svg; ?>'; this.onerror=null;" alt="SpidItalia ID" /></button>
                    </li>
                    <li class="spid-idp-support-link">
                        <a href="http://www.spid.gov.it">Maggiori info</a>
                    </li>
                    <li class="spid-idp-support-link">
                        <a href="http://www.spid.gov.it/#registrati">Non hai SPID?</a>
                    </li>
                <li class="spid-idp-support-link">
                    <a href="https://www.spid.gov.it/serve-aiuto">Serve aiuto?</a>
                </li>
                </ul>
            </div>
        </form>
        <?php
});

add_action( 'login_enqueue_scripts', function() {
    wp_enqueue_style( 'core', plugins_url( 'css/spid-sp-access-button.min.css', __FILE__ ), false );
}, 10 );

add_action( 'login_enqueue_scripts', function() {
    wp_enqueue_script( 'spid-js', plugins_url( 'js/spid-sp-access-button.min.js', __FILE__ ), array( 'jquery' )  );
}, 1 );

function spid_option($name) {
	$options = get_option('spid');
	if (isset($options[$name])) {
		return $options[$name];
	}
	return false;
}

?>
