<?php 

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}

require_once("twitch_api.php");

if(!class_exists("TwitchStreams_Connector")){
    class TwitchStreams_Connector {
        private static function hash($str) {
            $crc = crc32($str);
            return "tsthash(".strval($crc).")";
        }

        private static function getTime(){
            return get_option("twitchstreams_streams_cache", 10);
        }

        private const streams_key = "twitchstreams_streams";
        static function streams($rawchannels){
            $twitch = TwitchStreams_TwitchAPI::get();
            $cached = get_transient(self::streams_key);
            $hash = self::hash($rawchannels);
            if($cached === false || $hash != $cached["hash"]){
                $channels = explode(",", $rawchannels);
                $response = $twitch->streams($channels);
                if($response === null){
                    return null;
                }
                $streams = $response["data"];
                set_transient(self::streams_key, array(
                    'hash' => $hash,
                    'streams' => $streams
                ), self::getTime());
            }else{
                $streams = $cached["streams"];
            }
            return $streams;
        }

        private const transformedusers_key = "twitchstreams_transformedusers";
        static function transformedUsers($rawchannels){
            $twitch = TwitchStreams_TwitchAPI::get();
            $cached = get_transient(self::users_key);
            $hash = self::hash($rawchannels);

            $time = self::getTime() * 60;
            if($time > 30*60){
                $time = 30*60;
            }

            if($cached === false || $hash != $cached["hash"]){
                $response = self::users($rawchannels);
                if($response === null){
                    return null;
                }
                $users = $response;
                $transformed = array();
                foreach($users as $user){
                    $transformed[strval($user["id"])] = $user;
                }
                set_transient(self::users_key, array(
                    'hash' => $hash,
                    'users' => $transformed
                ), $time);
            }else{
                $transformed = $cached["users"];
            }
            return $transformed;
        }

        private const users_key = "twitchstreams_users";
        static function users($rawchannels){
            $twitch = TwitchStreams_TwitchAPI::get();
            $cached = get_transient(self::users_key);
            $hash = self::hash($rawchannels);

            $time = self::getTime() * 60;
            if($time > 30*60){
                $time = 30*60;
            }
            $time -= 15;

            if($cached === false || $hash != $cached["hash"]){
                $channels = explode(",", $rawchannels);
                $response = $twitch->users($channels);
                if($response === null){
                    return null;
                }
                $users = $response["data"];
                set_transient(self::users_key, array(
                    'hash' => $hash,
                    'users' => $users
                ), self::getTime());
            }else{
                $users = $cached["users"];
            }
            return $users;
        }


    }
}

?>