<?php

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}

require_once("connector.php");

if(!class_exists("TwitchStreams_Display")){
    class TwitchStreams_Display {

        static private function debug($var){
            ?>
            <code><?php echo var_dump($var) ?></code>
            <?php
        }

        static private function generateStreamTranslate($stream){
            $imgurl = strtr($stream["thumbnail_url"], array(
                '{width}' => '192',
                '{height}' => '108'
            ));

            $translate = array(
                '$username' => htmlspecialchars($stream['user_name']),
                '$title' => htmlspecialchars($stream['title']),
                '$viewers' => $stream['viewer_count'],
                '$thumbnailurl' => $imgurl
            );
            return $translate;
        }
        
        static private function generateUserdataTranslate($userdata){
            $translate = array(
                '$type' => htmlspecialchars($userdata['type']),
                '$displayname' => htmlspecialchars($userdata['display_name']),
                '$avatarurl' => $userdata['profile_image_url'],
                '$offlinethumbnailurl' => $userdata['offline_image_url']
            );
            return $translate;
        }

        private const cache_key = "twitchstreams_streams";
    
        const streamTemplate = '
<div class="tstr-stream">
    <img src="$thumbnailurl" class="tstr-image">
    <div class="tstr-midinfo">
        <span class="tstr-title">$title</span><br>
        <span class="tstr-username">$displayname</span>
    </div>
    <div class="tstr-viewers">$viewers</div>
</div>';
    
        static private function renderStream($stream, $transformed){
            if(boolval(get_option('twitchstreams_streamtemplatedefault'))){
                $template = self::streamTemplate;
            }else{
                $template = get_option('twitchstreams_streamtemplate', self::streamTemplate);
            }
    
            if($transformed !== null && array_key_exists($stream["user_id"], $transformed)){
                $userdata = $transformed[$stream["user_id"]];
            }else{
                $userdata = null;
            }

            $translate = self::generateStreamTranslate($stream);
            if($userdata !== null){
                $translate = array_merge($translate, self::generateUserdataTranslate($userdata));
            }
            
            return strtr($template, $translate);
        }

        const offlineTemplate = '
<div class="tstr-stream">
    <img src="$avatarurl" class="tstr-image">
    <div class="tstr-midinfo">
        <span class="tstr-title">$displayname</span><br>
        <span class="tstr-username">$type</span>
    </div>
</div>';

        static private function renderOffline($userdata){
            if(boolval(get_option('twitchstreams_offlinetemplatedefault'))){
                $template = self::offlineTemplate;
            }else{
                $template = get_option('twitchstreams_offlinetemplate', self::offlineTemplate);
            }

            $translate = self::generateUserdataTranslate($userdata);
            return strtr($template, $translate);
        }
    
        static private function renderStreams($streams, $transformed){
            $output = "";
            $onlineSet = array();
            $counter = 0;
            $limit = get_option("twitchstreams_limit");
            $useSeparateOffline = boolval(get_option("twitchstreams_useofflinetemplate"));

            if(is_array($streams)){
                foreach($streams as $stream){
                    if($limit > 0 && $counter >= $limit){
                        break;
                    }
                    $counter++;
                    $onlineSet[$stream["user_id"]] = true;
                    $output .= self::renderStream($stream, $transformed);
                }
            }
            if(is_array($transformed) && boolval(get_option("twitchstreams_showoffline"))){
                foreach($transformed as $userid => $userdata){
                    if($limit > 0 && $counter >= $limit){
                        break;
                    }
                    $counter++;
                    if(!array_key_exists($userid, $onlineSet)){
                        if($useSeparateOffline){
                            $output .= self::renderOffline($userdata);
                        }else{
                            $output .= self::renderStream(array(
                                "user_id" => $userid,
                                "user_name" => $userdata['display_name'],
                                "title" => "Stream is offline",
                                "thumbnail_url" => "",
                                "viewer_count" => 0
                            ), $transformed);
                        }
                    }
                }
            }
            return $output;
        }
    
        public const mainTemplate = '
<div class="tstr-main">
    <div>$streams</div>
</div>';
    
        static public function renderer(){
            if(boolval(get_option('twitchstreams_maintemplatedefault'))){
                $template = self::mainTemplate;
            }else{
                $template = get_option('twitchstreams_maintemplate', self::mainTemplate);
            }
            
            $channels = get_option("twitchstreams_channels");
            $streams = TwitchStreams_Connector::streams($channels);
            $transformed = TwitchStreams_Connector::transformedUsers($channels);
            return strtr($template, array(
                '$streams' => self::renderStreams($streams, $transformed)
            ));
        }
    }
}