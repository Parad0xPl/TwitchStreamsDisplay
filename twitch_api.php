<?php
if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}

if(!class_exists("TwitchStreams_TwitchAPI")){
    class TwitchStreams_TwitchAPI{
        static private $instance; 
        static public function get(){
            if(empty(self::$instance)){
                self::$instance = new self();
            }
            return self::$instance;
        }
    
        private $api_url = "https://api.twitch.tv/helix";
    
        private $client_id;
        
        function __construct(){
    
            $token = get_option("twitchstreams_twitch_token");
            $this->client_id = empty($token)? "" : $token ;
        }
    
        public static function build_query($args){
            $output = "";
            if(is_array($args)){
                foreach($args as $key => $value){
                    if(!empty($value)){
                        if(!is_array($value)){
                            $value = array($value);
                        }
                        foreach($value as $val){
                            $serialized = urlencode($key) . "=" . urlencode($val);
                            if(strlen($output) > 0){
                                $output .= "&".$serialized;
                            }else{
                                $output = $serialized;
                            }
                        }
                    }
                }
            }
            return $output;
        }
    
        // Return null on error
        private function request( $url = '', $args = array() ) {
            if(empty($this->client_id)){
                return null;
            }
    
            if(is_array($args) && sizeof($args) > 0) {
                $url .= "?".$this->build_query($args);
            }
    
            $headers = array(
                'Client-ID' => $this->client_id
            );
            $response = wp_remote_get( $this->api_url . $url, array(
                'timeout' => 15,
                'headers' => $headers
            ));
    
            if ( is_wp_error( $response ) )
                return null;
            $result = wp_remote_retrieve_body( $response );
            if ( is_wp_error( $result ) ) {
                return null;
            }
            $result = json_decode( $result, true );
            return $result;
        }
    
        public function streams($users){
            if(!is_array($users)){
                throw new Error("Users needs to be an array");
            }
            return $this->request("/streams", array(
                // "user_login" => $users
            ));
        }
    }
    
}
?>