<?php
    if ( !defined( 'SPID__LIB_DIR' ) ) {
        define( 'SPID__LIB_DIR', plugin_dir_path( __FILE__ ) . 'lib/' );
    }

    if ( !defined( 'SPID__LIB_URL' ) ) {
        define( 'SPID__LIB_URL', plugin_dir_url( __FILE__ ) . 'lib/www' );
    }

    if ( !defined( 'SPID__PERM_DIR' ) ) {
        define( 'SPID__PERM_DIR', WP_CONTENT_DIR .'/spid' );
    }
    if ( !defined( 'SPID__CONFIG_DIR' ) ) {
        define( 'SPID__CONFIG_DIR', SPID__PERM_DIR .'/config' );
    }
    if ( !defined( 'SPID__CERT_DIR' ) ) {
        define( 'SPID__CERT_DIR', SPID__PERM_DIR .'/cert/' );
    }

    if ( !defined( 'SPID__TEMP_DIR' ) ) {
        define( 'SPID__TEMP_DIR', SPID__PERM_DIR .'/tmp' );
    }
    if ( !defined( 'SPID__DATA_DIR' ) ) {
        define( 'SPID__DATA_DIR', SPID__PERM_DIR .'/data' );
    }
    if ( !defined( 'SPID__LOG_DIR' ) ) {
        define( 'SPID__LOG_DIR', SPID__PERM_DIR .'/log' );
    }

    if ( !defined( 'SPID__METADATA_DIR' ) ) {
        define( 'SPID__METADATA_DIR', SPID__PERM_DIR .'/metadata' );
    }
?>