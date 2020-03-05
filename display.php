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
    <img src="$profile_image_url" class="tstr-image">
    <div class="tstr-midinfo">
        <span class="tstr-title">$title</span><br>
        <span class="tstr-username">$type</span>
    </div>
    <div class="tstr-viewers">$viewers</div>
</div>';
    
        static private function renderStream($stream, $transformed){
            $template = get_option('twitchstreams_streamtemplate', self::streamTemplate);
    
            $imgurl = strtr($stream["thumbnail_url"], array(
                '{width}' => '192',
                '{height}' => '108'
            ));
    
            if($transformed !== null && array_key_exists($stream["user_id"], $transformed)){
                $userdata = $transformed[$stream["user_id"]];
            }else{
                $userdata = null;
            }

            $translate = array(
                '$username' => htmlspecialchars($stream['user_name']),
                '$title' => htmlspecialchars($stream['title']),
                '$viewers' => $stream['viewer_count'],
                '$thumbnail' => $imgurl
            );
            if($userdata !== null){
                $translate = array_merge($translate, array(
                    '$type' => htmlspecialchars($userdata['type']),
                    '$displayname' => htmlspecialchars($userdata['display_name']),
                    '$avatarurl' => htmlspecialchars($userdata['profile_image_url'])
                ));
            }
            
            return strtr($template, $translate);
        }
    
        static private function renderStreams($streams, $transformed){
            $output = "";
            if(is_array($streams)){
                foreach($streams as $stream){
                    $output .= self::renderStream($stream, $transformed);
                }
            }
            return $output;
        }
    
        public const mainTemplate = '
<div class="tstr-main">
    <div>%s</div>
</div>';
    
        static public function renderer(){
            $template = get_option('twitchstreams_maintemplate', self::mainTemplate);
            
            $channels = get_option("twitchstreams_channels");
            $streams = TwitchStreams_Connector::streams($channels);
            $transformed = TwitchStreams_Connector::transformedUsers($channels);
            return sprintf($template, self::renderStreams($streams, $transformed));
        }
    }
}