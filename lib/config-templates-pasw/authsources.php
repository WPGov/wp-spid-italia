<?php

$config = array(
    // This is a authentication source which handles admin authentication.
    'admin' => array(
        // The default is to use core:AdminPassword, but it can be replaced with
        // any authentication source.
        'core:AdminPassword',
    ),
    // An authentication source which can authenticate against both SAML 2.0
    // and Shibboleth 1.3 IdPs.
    'default-sp' => array(
        'saml:SP',
        'privatekey' => 'saml.pem',
        'certificate' => 'saml.crt',
        // The entity ID of this SP.
        // Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
        'entityID' => null,
        // The entity ID of the IdP this should SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => null,
        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => null,

        /*
         * WARNING: SHA-1 is disallowed starting January the 1st, 2014.
         *
         * Uncomment the following option to start using SHA-256 for your signatures.
         * Currently, SimpleSAMLphp defaults to SHA-1, which has been deprecated since
         * 2011, and will be disallowed by NIST as of 2014. Please refer to the following
         * document for more information:
         *
         * http://csrc.nist.gov/publications/nistpubs/800-131A/sp800-131A.pdf
         *
         * If you are uncertain about identity providers supporting SHA-256 or other
         * algorithms of the SHA-2 family, you can configure it individually in the
         * IdP-remote metadata set for those that support it. Once you are certain that
         * all your configured IdPs support SHA-2, you can safely remove the configuration
         * options in the IdP-remote metadata set and uncomment the following option.
         *
         * Please refer to the hosted SP configuration reference for more information.
         */

        /*
         * The attributes parameter must contain an array of desired attributes by the SP.
         * The attributes can be expressed as an array of names or as an associative array
         * in the form of 'friendlyName' => 'name'.
         * The metadata will then be created as follows:
         * <md:RequestedAttribute FriendlyName="friendlyName" Name="name" />
         */
        /* 'attributes' => array(
          'attrname' => 'urn:oid:x.x.x.x',
          ), */
        /* 'attributes.required' => array (
          'urn:oid:x.x.x.x',
          ), */


        /* Impostare il livello di spid che si vuole (1,2,3)  per il servizio la stringa sottostante non è più corretta
         *
         * 'urn:oasis:names:tc:SAML:2.0:ac:classes:SpidL1',
         *   https://www.agid.gov.it/sites/default/files/repository_files/documentazione/spid-avviso-n5-regole-tecniche-errata-corrige.pdf
         *   stringhe corrette
         *  'https://www.spid.gov.it/SpidL2',
         *  'https://www.spid.gov.it/SpidL3',
         */

        'AuthnContextClassRef' =>
        array(
            'https://www.spid.gov.it/SpidL1',
        ),

        'AuthnContextComparison' => 'minimum',

        /*Per autenticazione superiori a SPID Livello 1 occorre specificare 'ForceAuthn' => true */
        'ForceAuthn' => true,
	 // CHG added next 2 lines
        'NameIDPolicy' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
        'WantAssertionsSigned' => true,

        'AttributeConsumingServiceIndex' => 0,
        'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.sign.enable' => true,
        'metadata.sign.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'metadata.supported.protocols' => array('urn:oasis:names:tc:SAML:2.0:protocol'),


        'sign.authnrequest' => true,
        'sign.logout' => true,

        'OrganizationName' => array(
            'it' => '@organizationName / @localityName',
        ),
        'OrganizationDisplayName' => array(
            'it' => '@organizationName / @localityName',
        ),
        'OrganizationURL' => array(
            'it' => 'https://@commonName',
        ),
        'name' => array(
            'it' => '@machineName',
        ),
        'description' => array(
            'it' => '@organizationName / @localityName (@stateOrProvinceName)',
        ),
        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic',

        /* Per avere gli attributi richiesti tramite il metadata (codice fiscale, ecc) */
        'attributes' => array(
            'spidCode',
            'fiscalNumber', //codice fiscale (OBBLIGATORIO)
#			 'ivaCode', // partita IVA
#            'idCard', // Documento d'identità
#            'expirationDate', // Data di scadenza identità
            'familyName', // cognome (OBBLIGATORIO)
            'name',  // nome (OBBLIGATORIO)
            'gender', // sesso
            'dateOfBirth', // data di nascita
#            'placeOfBirth', // luogo di nascita
#            'countyOfBirth', // provincia di nascita
#            'companyName',  // Ragione o denominazione sociale
#            'registeredOffice', // Sede legale
#            'address', // domicilio fisico
#            'digitalAddress' // Indirizzo casella PEC
            'email', // email
#           'mobilePhone', // cellulare
        ),

        'acs.Bindings' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
        'SingleLogoutServiceBinding' => array('urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'),
    ),
);


