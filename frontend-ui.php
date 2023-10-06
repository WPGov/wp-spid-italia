<?php

function spid_get_idp_list( $showinfo = false, $spid_redirect_to = '' ) {
    $return = '';

    $plugin_dir = plugin_dir_url( __FILE__ );
    $spid_ico_circle_svg = $plugin_dir . '/img/spid-ico-circle-bb.svg';
    $spid_ico_circle_png = $plugin_dir . '/img/spid-ico-circle-bb.png';

    if ( spid_option('enable_validator') ) {
        $provider = array(
            array(
                'SPID validator',
                'https://validator.spid.gov.it',
                'test',
                0
            )
        );
        $provider[] = array( 'SPID Local (testenv2)', 'http://localhost:8088/sso', 'localhost', 'local' );
        $provider[] = array( 'SPID Local (saml)', 'https://localhost:8443/samlsso', 'localhost-saml', 'localsaml' );
        $provider[] = array( 'SPID TEST', 'https://demo.spid.gov.it/samlsso', 'demo', 'demo' );
    } else {
        $provider = array();
    }
    $shuffle = array();
    $shuffle[] = array( 'Infocert ID', 'https://identity.infocert.it', 'infocertid', 1 );
    $shuffle[] = array( 'Poste ID', 'https://posteid.poste.it', 'posteid', 2 );
    $shuffle[] = array( 'Tim ID', 'https://login.id.tim.it/affwebservices/public/saml2sso', 'timid', 3 );
    $shuffle[] = array( 'Sielte ID', 'https://identity.sieltecloud.it', 'sielteid', 4 );
    $shuffle[] = array( 'Aruba ID', 'https://loginspid.aruba.it', 'arubaid', 5 );
    $shuffle[] = array( 'Namirial ID', 'https://idp.namirialtsp.com/idp', 'namirialid', 6 );
    $shuffle[] = array( 'SpidItalia ID', 'https://spid.register.it', 'spiditalia', 7 );
    $shuffle[] = array( 'Intesi ID', 'https://idp.intesigroup.com', 'intesigroupspid', 8 );
    $shuffle[] = array( 'Lepida ID', 'https://id.lepida.it/idp/shibboleth', 'lepidaid', 9 );
    $shuffle[] = array( 'TeamSystem ID', 'https://spid.teamsystem.com/idp', 'teamsystemid', 10 );
    $shuffle[] = array( 'Etna ID', 'https://id.eht.eu', 'etnaid', 11 );
    $shuffle[] = array( 'InfoCamere ID', 'https://loginspid.infocamere.it', 'infocamereid', 12 );
    shuffle( $shuffle );
    $provider = array_merge( $provider, $shuffle );
    
    $return .= '<ul id="spid-idp-list-small-root-get" class="spid-idp-button-menu" aria-labelledby="spid-idp">';
    foreach ( $provider as $p ) {
        $url = wp_spid_italia_get_login_url();
        $url = add_query_arg( 'spid_sso', 'in', $url );
        $url = add_query_arg( 'spid_idp', $p[3], $url );
        if ( $spid_redirect_to ) {
            if ( $spid_redirect_to == 'CURRENT_URL' ) {
                $spid_redirect_to = $_SERVER['REQUEST_URI'];
            }
            $url = add_query_arg( 'spid_redirect_to', $spid_redirect_to, $url );
        }

        $return .= '<li class="spid-idp-button-link" data-idp="'.$p[2].'"><a href="'.esc_url( $url ).'" alt="'.$p[0].'"><img class="spid-provider" src="'.$plugin_dir.'img/idp/spid-idp-'.$p[2].'.svg" alt="'.$p[0].'" /></a></li>';
    }
    if ( $showinfo ) {
        $return .= '<li class="spid-idp-support-link"><a href="https://www.spid.gov.it">Maggiori informazioni</a></li>';
        $return .= '<li class="spid-idp-support-link"><a href="https://www.spid.gov.it/richiedi-spid">Non hai SPID?</a></li>';
        $return .= '<li class="spid-idp-support-link"><a href="https://www.spid.gov.it/serve-aiuto">Serve aiuto?</a></li>';
    }
    $return .= '</ul>';
    return $return;
}


function spid_get_loginform_button() {
    $return = '';

    $plugin_dir = plugin_dir_url( __FILE__ );
    $spid_ico_circle_svg = $plugin_dir . '/img/spid-ico-circle-bb.svg';
    $spid_ico_circle_png = $plugin_dir . '/img/spid-ico-circle-bb.png';

    $return .= '<!-- Generato con WP SPID Italia v.' . sanitize_text_field( get_option('spid_version') ) . '-->';

    $return .= '<div style="text-align:center;">';
    $return .= '<a href="#" class="italia-it-button italia-it-button-size-m button-spid" aria-haspopup="true" aria-expanded="false" id="spid-toggle">';
    $return .= '<span class="italia-it-button-icon"><img src="'.$spid_ico_circle_svg.'" onerror="this.src=\''.$spid_ico_circle_png.'\'; this.onerror=null;" alt="" /></span>';
    $return .= '<span class="italia-it-button-text">Entra con SPID</span>';
    $return .= '</a>';
    $return .= '<div id="spid-login-desc">SPID è il sistema di accesso che consente di utilizzare, con un\'identità digitale unica, i servizi online della Pubblica Amministrazione e dei privati accreditati.</div>';
    $return .= '<div style="font-size:0.8em;margin:0 0 10px 0;font-weight:bold;">';
    $return .= '<a href="http://www.spid.gov.it/#registrati">Non hai SPID?</a> &bull; <a href="http://www.spid.gov.it">Maggiori info</a>';
    $return .= '</div>';
    $return .= '<img src="'.$plugin_dir . '/img/spid-agid-logo-lb.png" width="200px" alt="Agenzia per l\'Italia Digitale" />';
    $return .= '</div>';
    
        return $return;
}

function spid_get_login_button_link( $size = 's' ) {
    
    $return = '';

    $plugin_dir = plugin_dir_url( __FILE__ );
    $spid_ico_circle_svg = $plugin_dir . '/img/spid-ico-circle-bb.svg';
    $spid_ico_circle_png = $plugin_dir . '/img/spid-ico-circle-bb.png';

    $return .= '<!-- Generato con WP SPID Italia v.' . sanitize_text_field( get_option('spid_version') ) . '-->';

    $return .= '<div class="spid-login-button">';
    $return .= '<a href="'.esc_url( wp_login_url() ).'" class="italia-it-button italia-it-button-size-'.$size.' button-spid">';
    $return .= '<span class="italia-it-button-icon"><img src="'.$spid_ico_circle_svg.'" onerror="this.src=\''.$spid_ico_circle_png.'\'; this.onerror=null;" alt="" /></span>';
    $return .= '<span class="italia-it-button-text">Entra con SPID</span>';
    $return .= '</a>';
    $return .= '</div>';

    return $return;
}

function spid_get_login_button( $size = 's', $redirectTo = '' ) {

    $return = '';

    $plugin_dir = plugin_dir_url( __FILE__ );
    $spid_ico_circle_svg = $plugin_dir . '/img/spid-ico-circle-bb.svg';
    $spid_ico_circle_png = $plugin_dir . '/img/spid-ico-circle-bb.png';

    $return .= '<!-- Generato con WP SPID Italia v.' . sanitize_text_field( get_option('spid_version') ) . '-->';

    $return .= '<div class="spid-login-button">';
    $return .= '<a href="#" class="italia-it-button italia-it-button-size-'.$size.' button-spid" spid-idp-button="#spid-idp-button-small-get" aria-haspopup="true" aria-expanded="false">';
    $return .= '<span class="italia-it-button-icon"><img src="'.$spid_ico_circle_svg.'" onerror="this.src=\''.$spid_ico_circle_png.'\'; this.onerror=null;" alt="" /></span>';
    $return .= '<span class="italia-it-button-text">Entra con SPID</span>';
    $return .= '</a>';
    $return .= '</div>';

    $return .= '<div id="spid-idp-button-small-get" class="spid-idp-button spid-idp-button-tip spid-idp-button-relative">';
    $return .= '<ul id="spid-idp-list-small-root-get" class="spid-idp-button-menu" aria-labelledby="spid-idp">';

    $return .= spid_get_idp_list( true, $redirectTo );

    $return .= '</ul>';
    $return .= '</div>';
    return $return;
}

?>
