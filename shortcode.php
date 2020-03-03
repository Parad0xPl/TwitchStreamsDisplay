<?php

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}

require_once("display.php");

add_shortcode('twitchstreams', array("TwitchStreams_Display", "renderer"));

?>