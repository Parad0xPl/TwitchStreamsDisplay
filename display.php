<?php

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}

require_once("connector.php");

if(!class_exists("TwitchStreams_Display")){
    class TwitchStreams_Display {

        private const cache_key = "twitchstreams_streams";
    
        const streamTemplate = '
        <div class="tstr-stream">
            <img src="$img" class="tstr-image">
            <div class="tstr-midinfo">
                <span class="tstr-title">$title</span><br>
                <span class="tstr-username">$username</span>
            </div>
            <div class="tstr-viewers">$viewers</div>
        </div>';
    
        static private function renderStream($stream){
            $template = self::streamTemplate;
    
            $imgurl = strtr($stream["thumbnail_url"], array(
                '{width}' => '192',
                '{height}' => '108'
            ));
    
            return strtr($template, array(
                '$username' => htmlspecialchars($stream['user_name']),
                '$title' => htmlspecialchars($stream['title']),
                '$viewers' => $stream['viewer_count'],
                '$img' => $imgurl
            ));
        }
    
        static private function renderStreams($streams){
            $output = "";
            if(is_array($streams)){
                foreach($streams as $stream){
                    $output .= self::renderStream($stream);
                }
            }
            return $output;
        }
    
        public const mainTemplate = '
        <div class="tstr-main">
            <!--<code>%s</code>-->
            <div>%s</div>
        </div>';
    
        static public function renderer(){
            
            $template = self::mainTemplate;
            
            $streams = TwitchStreams_Connector::streams(get_option("twitchstreams_channels"));
            return sprintf($template, print_r($streams ,TRUE), self::renderStreams($streams));
        }
    }
}