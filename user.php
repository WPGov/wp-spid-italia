<?php 
add_action('show_user_profile', 'spid_user_profile_fields', 0, 1);
add_action('edit_user_profile', 'spid_user_profile_fields', 0, 1);

add_action('personal_options_update', 'spid_user_profile_fields_update');
add_action('edit_user_profile_update', 'spid_user_profile_fields_update');

function spid_user_profile_fields($user) {
    ?>
    <h3>SPID</h3>
    <table class="form-table">
        <tr>
            <th>
                <label for="codice_fiscale">Codice Fiscale</label>
            </th>
            <td>
                <input type="text"
                       class="regular-text ltr"
                       id="codice_fiscale"
                       name="codice_fiscale"
                       value="<?= esc_attr(get_user_meta($user->ID, 'codice_fiscale', true)); ?>"
                       pattern="^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$">
            </td>
        </tr>
        <tr>
            <th>
                <label for="spid_attributes">Attributi SPID</label>
            </th>
            <td>
                <?php print_r( get_user_meta( $user->ID, 'spid_attributes') ); ?>
            </td>
        </tr>
    </table>
    <?php
}
 
/**
 * The save action.
 *
 * @param $user_id int the ID of the current user.
 *
 * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function spid_user_profile_fields_update($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
 
    return update_user_meta( $user_id, 'codice_fiscale', $_POST['codice_fiscale'] );
}

?>