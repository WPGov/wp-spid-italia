<?php

function spid_get_tabs( $id ) {

  $id0 = $id1 = $id2 = $id3 = '';
  switch ( $id ) {
    case 0:
      $id0 = ' nav-tab-active';
      break;
    case 1:
      $id1 = ' nav-tab-active';
      break;
    case 2:
      $id2 = ' nav-tab-active';
      break;
    }
  $r = '<h2 class="nav-tab-wrapper wp-clearfix">
    <a href="?page=spid_menu" class="nav-tab'.$id0.'">Home</a>
    <a href="?page=spid_menu&spid_action=option" class="nav-tab'.$id1.'">Impostazioni</a>
    <a href="?page=spid_menu&spid_action=metadata" class="nav-tab'.$id2.'">Metadata</a>
  </h2>';
  return $r;
}

function spid_menu_func() {
  echo '

  <div class="wrap about-wrap">

    <div style="background:#003d7a;border-radius:6px;padding:18px 24px;margin:16px 0 0;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
      <img src="'.esc_url( plugins_url( '../img/spid-logo-wp.png', __FILE__ ) ).'" alt="SPID" style="height:52px;width:auto;filter:brightness(0) invert(1);">
      <div style="flex:1;min-width:200px;">
        <div style="color:#fff;font-size:20px;font-weight:700;line-height:1.2;">WP SPID Italia</div>
        <div style="color:#a8c8f0;font-size:13px;margin-top:3px;">Versione 2.13.1</div>
        <div style="color:#a8c8f0;font-size:13px;margin-top:3px;">Plugin di autenticazione SPID per WordPress</div>
      </div>
      <div style="display:flex;gap:4px;flex-wrap:wrap;align-items:flex-end;flex-direction:column;">
        <a href="https://www.marcomilesi.com" target="_blank" style="color:#a8c8f0;font-size:13px;text-decoration:none;white-space:nowrap;">&#8599; marcomilesi.com</a>
        <a href="https://www.wpgov.it" target="_blank" style="color:#a8c8f0;font-size:13px;text-decoration:none;white-space:nowrap;">&#8599; wpgov.it</a>
      </div>
    </div>';
  
  if ( isset($_GET['spid_action']) && $_GET['spid_action'] == 'option' ) {
    echo spid_get_tabs( 1 );
    echo '<form method="post" action="options.php">';
    settings_fields( 'spid_options');
    $options = get_option( 'spid' );

    ?>
    <p class="about-description">
      Hai bisogno di aiuto? Consulta la <a href="https://github.com/WPGov/wp-spid-italia/wiki" alt="Documentazione" target="_blank">documentazione</a> o <a href="https://www.wpgov.it/contatti" alt="Contatti" target="_blank">contattaci</a> per una consulenza.
    </p>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">
          <label for="at_option_opacity">Abilita Login SPID</label>
        </th>
        <td>
          <input id="at_option_opacity" name="spid[enabled]" type="checkbox" value="1" <?php checked( '1', isset($options['enabled'])  ); ?> />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="enable_validator">Abilita Validatore</label>
        </th>
        <td>
          <input id="enable_validator" name="spid[enable_validator]" type="checkbox" value="1" <?php checked( '1', isset($options['enable_validator'])  ); ?> />
          Attiva i provider di test (validator, localhost e Demo SPID); lasciare questa opzione disattiva quando non sono in corso test.<br>
          <small><em>In caso di problemi con Demo SPID, sostituire il file <code>/wp-spid-italia/metadata/idp_demo.xml</code> con il metadata aggiornato da <a href="https://demo.spid.gov.it/metadata.xml" target="_blank">https://demo.spid.gov.it/metadata.xml</a></em></small>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="countryName">countryName</label>
        </th>
        <td>
          <input id="countryName" name="spid[countryName]" type="text" value="<?php echo ( isset( $options['countryName']) ? $options['countryName'] : '' ); ?>" />
          Codice ISO 3166-1 α-2 del Paese ove è situata la sede legale del SP (esempio: IT)
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="stateOrProvinceName">stateOrProvinceName</label>
        </th>
        <td>
          <input id="stateOrProvinceName" name="spid[stateOrProvinceName]" type="text" value="<?php echo ( isset( $options['stateOrProvinceName']) ? $options['stateOrProvinceName'] : '' ); ?>" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="localityName">localityName</label>
        </th>
        <td>
          <input id="localityName" name="spid[localityName]" type="text" value="<?php echo ( isset( $options['localityName']) ? $options['localityName'] : '' ); ?>" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="commonName">commonName</label>
        </th>
        <td>
          <input id="commonName" name="spid[commonName]" type="text" value="<?php echo ( isset( $options['commonName']) ? $options['commonName'] : '' ); ?>" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="emailAddress">emailAddress</label>
        </th>
        <td>
          <input id="emailAddress" name="spid[emailAddress]" type="text" value="<?php echo ( isset( $options['emailAddress']) ? $options['emailAddress'] : '' ); ?>" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="sp_org_name">sp_org_name</label>
        </th>
        <td>
          <input id="sp_org_name" name="spid[sp_org_name]" type="text" value="<?php echo ( isset( $options['sp_org_name']) ? $options['sp_org_name'] : '' ); ?>" maxlength="300"/> your organization full name
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="sp_org_display_name">sp_org_display_name</label>
        </th>
        <td>
          <input id="sp_org_display_name" name="spid[sp_org_display_name]" type="text" value="<?php echo ( isset( $options['sp_org_display_name']) ? $options['sp_org_display_name'] : '' ); ?>" maxlength="60"/> your organization display name
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="sp_contact_ipa_code">sp_contact_ipa_code</label>
        </th>
        <td>
          <input id="sp_contact_ipa_code" name="spid[sp_contact_ipa_code]" type="text" value="<?php echo ( isset( $options['sp_contact_ipa_code']) ? $options['sp_contact_ipa_code'] : '' ); ?>" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="sp_contact_fiscal_code">sp_contact_fiscal_code</label>
        </th>
        <td>
          <input id="sp_contact_fiscal_code" name="spid[sp_contact_fiscal_code]" type="text" value="<?php echo ( isset( $options['sp_contact_fiscal_code']) ? $options['sp_contact_fiscal_code'] : '' ); ?>" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="sp_contact_email">sp_contact_email</label>
        </th>
        <td>
          <input id="sp_contact_email" name="spid[sp_contact_email]" type="text" value="<?php echo ( isset( $options['sp_contact_email']) ? $options['sp_contact_email'] : '' ); ?>" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="sp_contact_phone">sp_contact_phone</label>
        </th>
        <td>
          <input id="sp_contact_phone" name="spid[sp_contact_phone]" type="text" value="<?php echo ( isset( $options['sp_contact_phone']) ? $options['sp_contact_phone'] : '' ); ?>" />
        </td>
      </tr>
    </table>
  <?php
  submit_button();
  echo '</form>';
  } else if ( isset($_GET['spid_action']) && $_GET['spid_action'] == 'metadata' ) {
    echo spid_get_tabs( 2 );
    echo '<p>Attenzione! Questi URL devono essere conservati con cura.</p><p>Non comunicare a terzi le informazioni di questa pagina e non indicarle su siti o forum di supporto.</p>';
    echo '<p class="about-description">URL metadata (service provider): <a href="'.spid_get_metadata_url().'" target="_blank">'.spid_get_metadata_url().'</a></p>';
    echo '<p class="about-description">URL metadata (aggregator) [beta]: <a href="'.spid_get_metadata_url( 'aggregator' ).'" target="_blank">'.spid_get_metadata_url( 'aggregator' ).'</a></p>';
  } else {
    echo spid_get_tabs( 0 );
    ?>
    <div class="changelog">
      <div class="under-the-hood two-col">
        <div class="col">
          <h3><a href="https://wordpress.org/plugins/wp-spid-italia/">Repository WordPress</a> &bull; <a href="https://github.com/WPGov/wp-spid-italia">Repository (GitHub)</a></h3>
          <p>Questo plugin open-source e disponibile su GitHub, dove puoi aprire una segnalazione o fare una pull request per migliorare il prodotto.</p>
        </div>
        <div class="col">
          <h3><a href="https://wordpress.org/plugins/wp-spid-italia/#developers">Changelog</a></h3>
          <p>Consulta le ultime migliorie apportate, nuove funzioni e lo storico di tutti i bugfix.</p>
        </div>
        <div class="col">
          <h3><a href="https://github.com/WPGov/wp-spid-italia/wiki">Wiki & Documentazione</a></h3>
          <p>Su GitHub sono disponibili guide e videotutorial semplici e intuitivi per implementare al meglio il plugin.</p>
        </div>
        <div class="col">
          <h3><a href="https://www.paypal.me/milesimarco">Donazione</a></h3>
          <p>Per fornirti tutto questo e garantire sviluppi futuri sono necessarie molte ore di lavoro. Se vuoi, puoi contribuire anche con un contributo economico.</p>
        </div>
      </div>
    </div>
    
    <h2>🤝</h2>
<h3 class="wp-people-group">Credits</h3>
<br><br>
<ul class="wp-people-group " id="wp-people-group-project-leaders">
<li class="wp-person">
<a href="https://www.marcomilesi.com" class="web"><img src="https://www.gravatar.com/avatar/c70b8e378aa035f77ab7a3ddee83b892" class="gravatar" alt="">
Marco Milesi</a>
<span class="title">Sviluppatore</span>
</li>
<li class="wp-person">
<a href="https://ct-net.it" class="web"><img src="https://www.gravatar.com/avatar/e3ba6cb4b821a6b5b68885bd14dc907b" class="gravatar" alt="">
Christian Ghellere</a>
<span class="title">Test e collaudo</span>
</li>
<li class="wp-person">
<a href="" class="web" style="pointer-events: none; cursor: default;"><img src="https://www.gravatar.com/avatar/9474c75c8be90627711a1e69d48f1797" class="gravatar" alt="">
Andrea Smith</a>
<span class="title">Test e collaudo</span>
</li>
</ul>

<p class="wp-credits-list">
Hanno collaborato alla fase di test: Gabriele Sonzogni
</p>

<h3 class="wp-people-group">Ringraziamenti</h3>
<br><br>
<ul class="wp-people-group " id="wp-people-group-core-developers">
  <li class="wp-person">
    <a href="" class="web" style="pointer-events: none; cursor: default;"><img src="https://www.gravatar.com/avatar/0" class="gravatar" alt="">
  Paolo Bozzo</a>
    <span class="title">Sviluppo libreria Drupal</span>
  </li>
  <li class="wp-person">
    <a href="" class="web" style="pointer-events: none; cursor: default;"><img src="https://www.gravatar.com/avatar/0" class="gravatar" alt="">
    Nadia Caprotti</a>
    <span class="title">Condivisione know-how Drupal</span>
  </li>
  <li class="wp-person">
    <a href="http://www.comune.fi.it" class="web"><img src="https://www.gravatar.com/avatar/0" class="gravatar" alt="">
    Comune di Firenze</a>
    <span class="title">Condivisione libreria</span>
  </li>
  <li class="wp-person">
    <a href="https://www.ils.org/" class="web"><img src="<?php echo plugins_url( '../img/ils.png', __FILE__ ); ?>" class="gravatar" alt="">
    Italian Linux Society</a>
    <span class="title">Contributo economico</span>
  </li>
  <li class="wp-person">
    <a href="http://www.porteapertesulweb.it/" class="web"><img src="https://avatars0.githubusercontent.com/u/7440334?s=50&v=4" class="gravatar" alt="">
    Porte Aperte sul Web</a>
    <span class="title">Spazio di confronto</span>
  </li>
</ul>
    <?php
  }
}
?>
