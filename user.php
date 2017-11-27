<?php 
add_action('show_user_profile', 'spid_user_profile_fields', 0, 1);
add_action('edit_user_profile', 'spid_user_profile_fields', 0, 1);

function spid_user_profile_fields( $user ) {
    echo '<h2><img src="'.SPID__PLUGIN_URL.'/img/spid-logo-b-lb.png'.'" alt="SPID" width="350px" /></h2>';
    echo 'Ultimo login: <b>'.'</b>';
    echo '<br>Attributi:<br><b><pre>'; print_r( get_user_meta( $user->ID, 'spid_attributes') ); echo '</pre></b>';
}

?>