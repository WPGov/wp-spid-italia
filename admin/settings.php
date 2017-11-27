<?php

function spid_get_tabs( $id ) {

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
    case 3:
      $id3 = ' nav-tab-active';
      break;
  }
  $r = '<h2 class="nav-tab-wrapper wp-clearfix">
    <a href="?page=spid_menu" class="nav-tab'.$id0.'">Benvenuto</a>
    <a href="?page=spid_menu&spid_action=make" class="nav-tab'.$id1.'">Configurazione</a>
    <a href="?page=spid_menu&spid_action=param" class="nav-tab'.$id2.'">Verifica</a>
    <a href="?page=spid_menu&spid_action=option" class="nav-tab'.$id3.'">Impostazioni</a>
    <a href="'.SPID__LIB_URL.'" class="nav-tab dashicons-before dashicons-welcome-widgets-menus" title="SimpleSaml Login"> </a>
  </h2>';
  return $r;
}

function spid_menu_func() {
  
  register_shutdown_function( function() { 
      $error = error_get_last();
      if ($error['type'] == 1) {
        session_destroy();
        die('<strong>ERRORE CERT</strong>');
      } 
  } );

  echo '
  <div class="wrap about-wrap">
  
  <a href="?page=spid_menu"><img src="'.plugins_url( '../img/spid-logo-wp.png', __FILE__ ).'" width="300px" style="padding:20px;" /></a>';
    
  if ( is_file (SPID__CERT_DIR.'/saml.pem') ) {
    if ( !spid_option('enabled') ) {
      echo '<p>Il login con SPID è disattivato. Attivalo dalle impostazioni del plugin dopo l\'approvazione come fornitore di servizi.</p>';
    }
  } else {
    echo '<p style="color:darkred;"><b>Non hai ancora inizializzato i certificati: utilizza il menù "Configurazione" per avviare la procedura.</b></p>';
  }

  if ( $_GET['spid_action'] == 'configure' ) {

    echo '<h3>Log di installazione</h3>';
    echo 'Avvio raccolta dati file di configurazione...<br>';

    $dn = array(
			"countryName" => sanitize_text_field( $_POST['countryName'] ),
			"stateOrProvinceName" => sanitize_text_field( $_POST['stateOrProvinceName'] ),
			"localityName" => sanitize_text_field( $_POST['localityName'] ),
			"organizationName" => sanitize_text_field( $_POST['organizationName'] ),
			"organizationalUnitName" => sanitize_text_field( $_POST['organizationalUnitName'] ),
			"commonName" => sanitize_text_field( $_POST['commonName'] ),
			"emailAddress" => sanitize_email( $_POST['emailAddress'] )
			);
    spid_make_certs($dn);
		$dn["admin_password"] = sanitize_text_field( $_POST['admin_password'] );
		$dn["machineName"] = sanitize_text_field( $_POST['machineName'] );
    $dn["libUrl"] = sanitize_text_field( $_POST['libURL'] );
    $dn["permDIR"] = sanitize_text_field( $_POST['permDIR'] );
    $dn["certDir"] = sanitize_text_field( $_POST['certDir'] );
    $dn["logDir"] = sanitize_text_field( $_POST['logDir'] );
    $dn["dataDir"] = sanitize_text_field( $_POST['dataDir'] );
    $dn["tempDir"] = sanitize_text_field( $_POST['tempDir'] );

    $dn["secretsalt"] = spid_rand_string(32);
    
    echo 'Memorizzazione dump...<br>';
    update_option('spid_dump', $dn);
    echo 'Memorizzazione dump <b>completata</b>!<br>';

    echo 'Scrittura config.php...<br>';
		spid_config_file( SPID__CONFIG_DIR . '/config.php', $dn);
    echo 'Scrittura config.php <b>completata</b>!<br>';
    echo 'Scrittura authsources.php...<br>';
		spid_config_file( SPID__CONFIG_DIR . '/authsources.php', $dn);
    echo 'Scrittura authsources.php <b>completata</b>!<br>';
    echo 'Installazione completata!';
    die();
  } else if ( $_GET['spid_action'] == 'make' ) {
    echo spid_get_tabs( 1 );

  if ( is_file (SPID__CERT_DIR.'/saml.pem') ) {
    echo '<div class="notice notice-info"><p>I certificati risultano già generati!</p></div>';
      echo '<p>Per ripetere il processo di configurazione eliminare le seguenti cartelle, poi riattivare il plugin.';
      echo '<ul><li>'.SPID__PERM_DIR.'</li></ul></p>';
      echo '<p><b>Attenzione:</b> se è già stata richiesta l\'attivazione SPID ad AGID la cancellazione dei file può comportare la perdita dei diritti acquisiti.<br>Consigliamo di procedere solo se necessario e di eseguire un backup delle cartelle e dei file rimossi.</p>';
  } else {
  echo '<p>Compilare correttamente <b>tutti</b> i campi per inizializzare i file di configurazione e generare i certificati.</p>';
  ?>
    <form id="get_data" action="?page=spid_menu&spid_action=configure" method="POST" onSubmit="return validator()">
  		<input name="countryName" type="text" value="IT" readonly /> countryName<br/>
  		<select name="stateOrProvinceName"><option>Agrigento</option><option>Alessandria</option><option>Ancona</option><option>Aosta</option><option>Arezzo</option><option>Ascoli Piceno</option><option>Asti</option><option>Avellino</option><option>Bari</option><option>Barletta-Andria-Trani</option><option>Belluno</option><option>Benevento</option><option>Bergamo</option><option>Biella</option><option>Bologna</option><option>Bolzano</option><option selected="selected">Brescia</option><option>Brindisi</option><option>Cagliari</option><option>Caltanissetta</option><option>Campobasso</option><option>Carbonia-Iglesias</option><option>Caserta</option><option>Catania</option><option>Catanzaro</option><option>Chieti</option><option>Como</option><option>Cosenza</option><option>Cremona</option><option>Crotone</option><option>Cuneo</option><option>Enna</option><option>Fermo</option><option>Ferrara</option><option>Firenze</option><option>Foggia</option><option>Forlì-Cesena</option><option>Frosinone</option><option>Genova</option><option>Gorizia</option><option>Grosseto</option><option>Imperia</option><option>Isernia</option><option>La Spezia</option><option>L&#039;Aquila</option><option>Latina</option><option>Lecce</option><option>Lecco</option><option>Livorno</option><option>Lodi</option><option>Lucca</option><option>Macerata</option><option>Mantova</option><option>Massa-Carrara</option><option>Matera</option><option>Messina</option><option>Milano</option><option>Modena</option><option>Monza e della Brianza</option><option>Napoli</option><option>Novara</option><option>Nuoro</option><option>Olbia-Tempio</option><option>Oristano</option><option>Padova</option><option>Palermo</option><option>Parma</option><option>Pavia</option><option>Perugia</option><option>Pesaro e Urbino</option><option>Pescara</option><option>Piacenza</option><option>Pisa</option><option>Pistoia</option><option>Pordenone</option><option>Potenza</option><option>Prato</option><option>Ragusa</option><option>Ravenna</option><option>Reggio Calabria</option><option>Reggio Emilia</option><option>Rieti</option><option>Rimini</option><option>Roma</option><option>Rovigo</option><option>Salerno</option><option>Medio Campidano</option><option>Sassari</option><option>Savona</option><option>Siena</option><option>Siracusa</option><option>Sondrio</option><option>Taranto</option><option>Teramo</option><option>Terni</option><option>Torino</option><option>Ogliastra</option><option>Trapani</option><option>Trento</option><option>Treviso</option><option>Trieste</option><option>Udine</option><option>Varese</option><option>Venezia</option><option>Verbano-Cusio-Ossola</option><option>Vercelli</option><option>Verona</option><option>Vibo Valentia</option><option>Vicenza</option><option>Viterbo</option></select> stateOrProvinceName<br/>
  		<input name="localityName" type="text" value=""/> localityName<br/>
  		<input name="organizationName" type="text" value=""/> organizationName<br/>
  		<input name="organizationalUnitName" type="text" value=""/> organizationalUnitName<br/>
  		<input name="commonName" type="text" value="<?php echo parse_url( get_site_url() )['host']; ?>"/> commonName(dominio)<br/>
      <input name="libURL" type="text" value="<?php echo SPID__LIB_URL; ?>"/> SPID__LIB_URL<br/>
      <input name="permDIR" type="text" value="<?php echo SPID__PERM_DIR; ?>"/> SPID__PERM_DIR<br/>
      <input name="dataDir" type="text" value="<?php echo SPID__DATA_DIR; ?>"/> SPID__DATA_DIR<br/>
      <input name="tempDir" type="text" value="<?php echo SPID__TEMP_DIR; ?>"/> SPID__TEMP_DIR<br/>
      <input name="logDir" type="text" value="<?php echo SPID__LOG_DIR; ?>"/> SPID__LOG_DIR<br/>
      <input name="certDir" type="text" value="<?php echo SPID__CERT_DIR; ?>"/> SPID__CERT_DIR<br/>
  		<input name="emailAddress" type="text" value=""/> emailAddress<br/>
  		<h3>Configurazione Libreria</h3>
  		<input id="pass1" name="admin_password" type="password" value=""/> password amministratore<br/>
  		<input id="pass2" name="admin_password2" type="password" value=""/> ripeti password amministratore<br/>
  		<input name="machineName" type="text" value=""/> nome macchina della scuola(senza spazi)<br/>
            <br><br>
          <input type="submit" class="button button-primary" value="Continua"/>
          <br><br><br>
  	</form>
  <?php
  }
  } else if ( $_GET['spid_action'] == 'param' ) {
    echo spid_get_tabs( 2 );

    echo '<p>Presta attenzione ai dati sensibili che comunichi a terzi.</p>';
    $yes = '<img style="float: left;padding: 10px;" src="'.plugins_url( '../img/yes.png', __FILE__ ).'" />';
    $no = '<img style="float: left;padding: 10px;" src="'.plugins_url( '../img/no.png', __FILE__ ).'" />';
    
    
    $headers = @get_headers( SPID__LIB_URL );
    (strpos($headers[0],'404') === false) ? $lib_url = true : $lib_url = false;
    
    echo '<table style="width:100%">
    <tr><td>'.( is_dir(SPID__PERM_DIR) ? $yes : $no ).'</td><td>SPID__PERM_DIR</td><td>'.SPID__PERM_DIR.'</td></tr>
    <tr><td>'.( is_dir(SPID__CONFIG_DIR) ? $yes : $no ).'</td><td>SPID__CONFIG_DIR</td><td>'.SPID__CONFIG_DIR.'</td></tr>
    <tr><td>'.( is_dir(SPID__CERT_DIR) ? $yes : $no ).'</td><td>SPID__CERT_DIR</td><td>'.SPID__CERT_DIR.'</td></tr>
    <tr><td>'.( is_dir(SPID__DATA_DIR) ? $yes : $no ).'</td><td>SPID__DATA_DIR</td><td>'.SPID__DATA_DIR.'</td></tr>
    <tr><td>'.( is_dir(SPID__TEMP_DIR) ? $yes : $no ).'</td><td>SPID__TEMP_DIR</td><td>'.SPID__TEMP_DIR.'</td></tr>
    <tr><td>'.( is_dir(SPID__LOG_DIR) ? $yes : $no ).'</td><td>SPID__LOG_DIR</td><td>'.SPID__LOG_DIR.'</td></tr>
    <tr><td>'.( is_dir(SPID__LIB_DIR) ? $yes : $no ).'</td><td>SPID__LIB_DIR</td><td>'.SPID__LIB_DIR.'</td></tr>
    <tr><td>'.( $lib_url ? $yes : $no ).'</td><td>SPID__LIB_URL</td><td>'.SPID__LIB_URL.'</td></tr>
    <tr><td>'.( is_file (SPID__CONFIG_DIR.'/config.php') ? $yes : $no ).'</td><td>SPID__CONFIG_DIR/config.php</td><td>'.SPID__CONFIG_DIR.'/config.php</td></tr>
    <tr><td>'.( is_file (SPID__CONFIG_DIR.'/authsources.php') ? $yes : $no ).'</td><td>SPID__CONFIG_DIR/authsources.php</td><td>'.SPID__CONFIG_DIR.'/authsources.php</td></tr>
    <tr><td>'.( is_file (SPID__CERT_DIR.'/saml.pem') ? $yes : $no ).'</td><td>SPID__CERT_DIR/saml.pem</td><td>'.SPID__CERT_DIR.'/saml.pem</td></tr>
    <tr><td>'.( is_file (SPID__CERT_DIR.'/saml.crt') ? $yes : $no ).'</td><td>SPID__CERT_DIR/salm.crt</td><td>'.SPID__CERT_DIR.'</td></tr>
    <tr><td>'.( is_file (SPID__PERM_DIR.'/.htaccess') ? $yes : $no ).'</td><td>SPID__PERM_DIR/.htaccess</td><td>'.SPID__PERM_DIR.'/.htaccess</td></tr>
    <tr><td>'.( is_file (SPID__LIB_DIR.'/.htaccess') ? $yes : $no ).'</td><td>SPID__LIB_DIR/.htaccess</td><td>'.SPID__LIB_DIR.'/.htaccess</td></tr>
    <tr><td>'.( is_file (SPID__LIB_DIR.'/www/.htaccess') ? $yes : $no ).'</td><td>SPID__LIB_DIR/www/.htaccess</td><td>'.SPID__LIB_DIR.'/www/.htaccess</td></tr>';
    
    $sd = get_option('spid_dump', true);
    if ( is_array($sd) ) {
      echo '<tr><td>'.$yes.'</td><td>ULTIMO DUMP</td><td>';
      print_r( $sd );
      echo '</td></tr>';
    } else {
      echo '<tr><td>'.$no.'</td><td>ULTIMO DUMP</td><td><b>Nessun dato presente</b><br>Eseguire la configurazione iniziale</td></tr>';
    }

  echo '</table>';

  } else if ( $_GET['spid_action'] == 'option' ) {
    echo spid_get_tabs( 3 );
    echo '<form method="post" action="options.php">';
    settings_fields( 'spid_options');
    $options = get_option( 'spid' );

    ?><table class="form-table">
          <tr valign="top">
    <th scope="row">
      <label for="at_option_opacity">Abilita Login SPID</label>
    </th>
    <td>
      <input id="at_option_opacity" name="spid[enabled]" type="checkbox" value="1"
             <?php checked( '1', isset($options['enabled'])  ); ?> />
    </td>
  </tr></table><?php
  submit_button();
  echo '</form>';
  } else {
    echo spid_get_tabs( 0 );
    
    include( plugin_dir_path( __FILE__ ) . 'welcome.php');
  }

  echo '</div>';
}

function spid_config_file($file, $array) {
	$lines = file($file);
	foreach ($lines as &$line)
		$line = spid_token_replace($line, $array);
	$handle = fopen($file, "w");
	if ($handle) {
		foreach ($lines as $line)
			fwrite ($handle, $line);
		fclose($handle);
	} else {
		echo "impossibile scrivere file di configurazione";
	}
}
function spid_rand_string( $length ) {
    $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
    $size = strlen( $chars );
    for( $i = 0; $i < $length; $i++ ) {
        $str .= $chars[ rand( 0, $size - 1 ) ];
    }
    return $str;
}

function spid_token_replace($string, $array) {
	if (strchr($string, '@')) {
		foreach ($array AS $key => $value){
			$val = str_replace("'", "\'", $value);
			$string = str_replace('@' . $key, $val, $string);
		}
	}
	return $string;
}

function spid_make_certs($dn) {
	$numberofdays = 3652 * 2;
	$privkey = openssl_pkey_new(array(
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
));
	$csr = openssl_csr_new($dn, $privkey, array('digest_alg' => 'sha256'));
	$serials = @file("serials.txt");
	if ($serials === false) $serials = array();
	do {
		$myserial = hexdec(bin2hex(openssl_random_pseudo_bytes(8)));
	} while (in_array ($myserial, $serials));
	$fh = fopen("serials.txt", "a");
	if ($fh) {
		fwrite($fh, sprintf("%d\n", $myserial));
		fclose($fh);
	}
	$configArgs = array("digest_alg" => "sha256");
	$sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays, $configArgs, $myserial);
	openssl_x509_export($sscert, $publickey);
	openssl_pkey_export($privkey, $privatekey);
	//openssl_csr_export($csr, $csrStr);
	file_put_contents( SPID__CERT_DIR . '/saml.pem', $privatekey);
	file_put_contents( SPID__CERT_DIR . '/saml.crt', $publickey);
	
	//echo $privatekey.'<br/>'; // Will hold the exported PriKey
	//echo $publickey.'<br/>';  // Will hold the exported PubKey
	//echo $csrStr;     // Will hold the exported Certificate
}
?>