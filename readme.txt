=== WP SPID Italia ===
Contributors: Milmor
Donate link: https://www.paypal.me/milesimarco
Tags: spid, italia, sistema, pubblico, identità, digitale, login, sistema pubblico di identità digitale, wpgov, marco, milesi, marco milesi
Requires at least: 4.8
Requires PHP: 7
Tested up to: 6.5
Version: 2.8.2
Stable tag: 2.8.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

SPID - Sistema Pubblico di Identità Digitale

== Description ==

Plugin WordPress per l'interfacciamento con il Sistema Pubblico di Identità Digitale (SPID)

https://www.youtube.com/watch?v=w5Z5EBG1R1M

* GitHub: https://github.com/WPGov/wp-spid-italia
* WP.org: https://wordpress.org/plugins/wp-spid-italia

https://www.youtube.com/watch?v=i2eTL_Q2xfM

Copyright © 2017-2024 **Marco Milesi**
[www.marcomilesi.com](https://www.marcomilesi.com) - [www.wpgov.it](https://www.wpgov.it)

### Ringraziamenti
* **Christian Ghellere, Andrea Smith**: attività di software testing
* **Paolo Bozzo**: sviluppo libreria Drupal-PASW
* **Nadia Caprotti**: condivisione know-how Drupal-PASW
* **Comune di Firenze**: sviluppo libreria SimpleSaml riadattata da Paolo
* **Porte Aperte sul Web**

### Sponsor
* **Italian Linux Society**

== Installation ==

Per informazioni dettagliate consultare la [documentazione](https://github.com/WPGov/wp-spid-italia/wiki).

### Videotutorial

https://www.youtube.com/watch?v=w1jf8GgF1OQ

https://www.youtube.com/watch?v=2UNAtVjFFAs

== Changelog ==
> Backup your data before upgrade.

= 2.8.2 20240226 =
* [BUGFIX] Aggiornato metadata validator.spid.gov.it - PR #68 https://github.com/WPGov/wp-spid-italia/pull/68
* [BUGFIX] Aumentato limite caratteri sp_org_display_name - PR #66 https://github.com/WPGov/wp-spid-italia/pull/66

= 2.8.1 20240118 =
* [BUGFIX] Aggiornato metadata validator.spid.gov.it

= 2.8 20231025 =
* [IMPROVE] Nuova gestione degli errori in caso di utenza non registrata (messaggio su standard WP)
* [IMPROVE] Revisione filtro in caso di nuove utenze con controllo classe WP_User (rif. wp-spid-italia.php:284)
* [BUGFIX] Corretto errore di mancata possibilità reset password WP in alcune configurazioni
* [BUGFIX] Migliorata la gestione degli errori in fase di login e errori in alcune configurazioni
* Testato con WordPress 6.4

= 2.7.1 20231004 =
* [BUGFIX] Error with missing reset password button with some configurations (thanks to effekt.it)
* Tested with WP 6.3

= 2.7 20230718 =
* [NEW] Removed IntesiGroup IDP
* [BUGFIX] Code and orphan resources cleanup

= 2.6.1 20230527 =
* [NEW] Added Infocamere IDP
* [NEW] Removed IntesaID IDP
* [BUGFIX] Minor bugfix

= 2.6 20230508 =
* [NEW] Added EtnaID IDP
* [BUGFIX] Minor css/code refactoring
* [BUGFIX] Minor bugfix

= 2.5 20221105 =
* [NEW] Added TeamSystem IDP
* [NEW] Improved wp-login.php UI/UX with 2 columns-layout
* [IMPROVE] WP 6.1 compatibility
* [BUGFIX] Fixed redirect url (thanks Daneel87)
* [BUGFIX] Minor css/code refactoring
* [BUGFIX] Minor bugfix

= 2.4.3 20220621 =
* [BUGFIX] Fixed bug causing login error with Namirial under some server configurations
* [BUGFIX] Improved logout handlink with post() spid interface
* [BUGFIX] Minor bugfix

= 2.4.1 20220531 =
* [NEW] Added redirect_to shortcode parameter with optional "CURRENT_URL" value (login redirect to current page)
* [IMPROVE] Added new Intesa logo

= 2.4 20220529 =
* [NEW] Added low-level support for digitalAddress, digitalAddress, domicileStreetAddress, domicilePostalCode, domicileMunicipality, domicileProvince, domicileNation (avviso 25)
* [NEW] Added shortcode parameter redirect_to
* [IMPROVE] WP 6.0 compatibility and best-pratices
* [IMPROVE] Better filters/api
* [BUGFIX] Minor bugfix

= 2.3.6 20220321 =
* [BUGFIX] Spid validator metadata update

= 2.3.5 20220318 =
* [IMPROVEMENT] Performance boost
* [SECURITY] Updated libraries with CVE **(!)**
* [BUGFIX] Minor bugfix

= 2.3.4 20220213 =
* [IMPROVEMENT] Added beta support for aggregator XML (enterprise light/full service aggregator)
* [IMPROVEMENT] Minor changes
* [BUGFIX] Added final trailing slash on XML metadata (stripped from WP 5.9) - Thanks to C. Lentini

= 2.3.3 20220201 =
* [IMPROVEMENT] Minor changes
* [IMPROVEMENT] WP 5.9+6.0 compatibility check
* [BUGFIX] Updated Inforcert metadata

= 2.3 20211230 =
* [NEW] Added compatibility with third party login URL modifiers and custom filters - check [wiki](https://github.com/WPGov/wp-spid-italia/wiki)
* [IMPROVEMENT] Performance boost
* [IMPROVEMENT] Settings page changes
* [IMPROVEMENT] Readme and doc changes
* [BUGFIX] Removed some warnings

= 2.2.4 20211201 =
* [BUGFIX] Minor bugfix - https://github.com/WPGov/wp-spid-italia/releases

= 2.2.3 20211016 =
* [BUGFIX] Minor bugfix - https://github.com/WPGov/wp-spid-italia/releases

= 2.2.2 20211019 =
* [BUGFIX] Minor bugfix - https://github.com/WPGov/wp-spid-italia/releases

= 2.2 20211017 =
* [NEW] Frontend button UI
* [NEW] Added various filters - check [wiki](https://github.com/WPGov/wp-spid-italia/wiki)
* [IMPROVEMENT] Various code refactoring
* [BUGFIX] Minor bugfix

= 2.1 20210930 =
* [NEW] Shortcode for f/e Spid button display
* [NEW] Added familyName (last_name) as default field to generate SPID crt (note: requires AGID metadata resubmission, if you want to use it)
* [NEW] Added name and familyName mapping to wp first_name and last_name
* [IMPROVEMENT] Improved WP enqueue logic
* [IMPROVEMENT] Code refactoring for html/css/js styling on both f/e and login form
* [BUGFIX] Added sp_org limit lenght
* [BUGFIX] Minor bugfix

= 2.0 =
* **Improved** graphic design
* **Improved** login error handling with WP standards

* Changed level type due to AGID corrige 'AuthnContextClassRef' => array( 'https://www.spid.gov.it/SpidL1' )
* Removed AllowCreate parameter, as requested by AGID - lib\modules\saml\lib\Auth\Source\SP.php
* RelayState change to encoded aes-265 - lib\vendor\simplesamlphp\saml2\src\SAML2\HTTPArtifact.php

= 1.5.2 - 20200926 =
* **Compatibility** check
* Minor changes

= Versione 1.5 09/10/2019 =
* **Fixed** SAML component security issue, xmlseclibs from 2.0.1 to 2.1.1
* Minor improvements

= Versione 1.4 11/09/2018 =
* **Aggiunto** messaggio di errore in caso di utente non riconosciuto (email/CF mancanti in WordPress)
* **Migliorata** interfaccia impostazioni

= Versione 1.3.2 16/12/2017 =
* **Fixed** php warnings on settings.php

= Versione 1.3.1 14/12/2017 =
* readme.txt changes

= Versione 1.3 13/12/2017 =
* **Nuovo** sistema di logout non più basato su wp_logout_url (testare)
* **Corretto** conflitto con wp_logout_url che causava un warning e un blocco dei menù a causa di caratteri speciali nel link di logout della libreria
* Rimosso parametro di autenticazione **AuthnContextClassRef** (testare) per problemi con un IDP

= Versione 1.1 27/11/2017 =
* **Aggiunto** sistema di login tramite codice fiscale
* **Aggiunto** campo utente codice_fiscale
* Miglioramenti vari
* Modifiche alla schermata impostazioni

= Versione 1.0.1 24/11/2017 =
* **Corretti** bug (falsi allarmi) nel pannello di verifica

= Versione 1.0 23/11/2017 =
* **Corrette** stringhe mancanti

= Version 0.7b 16/11/2017 =
* **Impostata lingua italiana** su libreria e rimosse altre localizzazioni

= Version 0.5b 16/11/2017 =
* Architecture bump for closed testing

= Versione 0.2 (beta) 14/11/2017 =
* First commit after plugin approval
