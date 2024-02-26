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

  <div id="welcome-panel" style="text-align: center; padding: 30px;">
    <a href="?page=spid_menu"><img src="'.plugins_url( '../img/spid-logo-wp.png', __FILE__ ).'" width="200px" style="padding:20px;" /></a>
    <h2>SPID | Sistema Pubblico di Identità Digitale</h2>
	  <p class="about-description">La soluzione che ti permette di accedere a tutti i servizi online della Pubblica Amministrazione con un\'unica Identità Digitale (username e password) utilizzabile da computer, tablet e smartphone.</p>
	</div>
  <div class="wrap about-wrap">';
  
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
          Lasciare questa opzione disattiva quando non sono in corso test
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
    
    include( plugin_dir_path( __FILE__ ) . 'welcome.php');
  }
}
?>
