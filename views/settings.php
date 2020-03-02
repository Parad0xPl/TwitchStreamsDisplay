<?php

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}
?>

<div class="wrap">
    <h1>Twitch Stream Settings</h1>
    <form action="options.php" method="post">
        <!-- <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th>Twitch Token</th>
                    <td><input type="text" class="regular-text" name="twitch_token" id="twitch_token"/></td>
                </tr>
            </tbody>
        </table> -->
        <?php
        settings_fields( 'twitchstreams_settings' );
        do_settings_sections( 'twitchstreams_settings' );
        submit_button('Save Changes', 'button-primary', 'submit', false );
        ?>
    </form>
</div>