<?php

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}

require_once("display.php");

function twitchstreams_shortcode(){
    wp_enqueue_style("twitchstreams-mainstyle");
    return TwitchStreams_Display::renderer();
}

add_shortcode('twitchstreams', "twitchstreams_shortcode");

?>