<?php

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}


require_once("display.php");

class TwitchStreamsSettings {
    static function init(){
        add_action("admin_menu", array("TwitchStreamsSettings", "addPage"));
        add_action("admin_init", array("TwitchStreamsSettings", "registerOptions"));
    }

    static function html(){
        if(!current_user_can( 'manage_options')){
            return;
        }
        include "views/settings.php";
    }

    static function addPage(){
        $options_hook = add_options_page(
            "Twitch Streams Settings",
            "Twitch Streams",
            "manage_options",
            "twitchstreams_options",
            array("TwitchStreamsSettings", "html")
        );
    }

    static function sanitize_cacheTime($val){
        $val = absint($val);
        if($val < 5){
            $val = 10;
        }
        return $val;
    }

    static function sanitize_int($val){
        $val = intval($val);
        return $val;
    }

    static function sanitize_bool($val){
        if($val !== "true"){
            $val = "";
        }else{
            $val = "1";
        }
        return $val;
    }

    static function registerOptions(){
        // register twitch settings
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_twitch_token');
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_channels');
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_streams_cache',
            array(
                'sanitize_callback' => array('TwitchStreamsSettings', 'sanitize_cacheTime'),
                'default' => 10
            )
        );
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_showoffline',
            array(
                'sanitize_callback' => array('TwitchStreamsSettings', 'sanitize_bool'),
                'default' => ""
            )
        );
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_limit',
            array(
                'sanitize_callback' => array('TwitchStreamsSettings', 'sanitize_int'),
                'default' => -1
            )
        );

        // register template settings
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_streamtemplatedefault',
            array(
                'sanitize_callback' => array('TwitchStreamsSettings', 'sanitize_bool'),
                'default' => "1"
            )
        );
        register_setting(
            'twitchstreams_settings',
            'twitchstreams_streamtemplate',
            array(
                'default' => TwitchStreams_Display::streamTemplate
            )
        );
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_maintemplatedefault',
            array(
                'sanitize_callback' => array('TwitchStreamsSettings', 'sanitize_bool'),
                'default' => "1"
            )
        );
        register_setting(
            'twitchstreams_settings',
            'twitchstreams_maintemplate',
            array(
                'default' => TwitchStreams_Display::mainTemplate
            )
        );
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_useofflinetemplate',
            array(
                'sanitize_callback' => array('TwitchStreamsSettings', 'sanitize_bool'),
                'default' => "1"
            )
        );
        register_setting(
            'twitchstreams_settings', 
            'twitchstreams_offlinetemplatedefault',
            array(
                'sanitize_callback' => array('TwitchStreamsSettings', 'sanitize_bool'),
                'default' => "1"
            )
        );
        register_setting(
            'twitchstreams_settings',
            'twitchstreams_offlinetemplate',
            array(
                'default' => TwitchStreams_Display::offlineTemplate
            )
        );

            
        // register twitch section
        add_settings_section(
            'twitchstreams_twitchsettings',
            'Twitch Settings',
            array("TwitchStreamsSettings", "twitchSectionRenderer"),
            'twitchstreams_settings'
        );
        // register template section
        add_settings_section(
            'twitchstreams_templatesection',
            'Templates',
            array("TwitchStreamsSettings", "templateSectionRenderer"),
            'twitchstreams_settings'
        );

        // register fields to twitch section
        add_settings_field(
            'twitchstreams_twitch_token',
            'Twitch Token',
            array("TwitchStreamsSettings", "twitchTokenRenderer"),
            'twitchstreams_settings',
            'twitchstreams_twitchsettings'
        );
        add_settings_field(
            'twitchstreams_channels',
            'Target Channels',
            array("TwitchStreamsSettings", "channelsRenderer"),
            'twitchstreams_settings',
            'twitchstreams_twitchsettings'
        );
        add_settings_field(
            'twitchstreams_streams_cache',
            'Streams Cache Time',
            array("TwitchStreamsSettings", "streamsCacheRenderer"),
            'twitchstreams_settings',
            'twitchstreams_twitchsettings'
        );
        add_settings_field(
            'twitchstreams_showoffline',
            'Show Offline Channels',
            array("TwitchStreamsSettings", "showOfflineRenderer"),
            'twitchstreams_settings',
            'twitchstreams_twitchsettings'
        );
        add_settings_field(
            'twitchstreams_limit',
            'Channels limit',
            array("TwitchStreamsSettings", "limitRenderer"),
            'twitchstreams_settings',
            'twitchstreams_twitchsettings'
        );

        // register fields to template
        add_settings_field(
            'twitchstreams_maintemplatedefault',
            'Use default main template?',
            array("TwitchStreamsSettings", "mainDefaultRenderer"),
            'twitchstreams_settings',
            'twitchstreams_templatesection'
        );
        add_settings_field(
            'twitchstreams_maintemplate',
            'Main Template',
            array("TwitchStreamsSettings", "mainTemplateRenderer"),
            'twitchstreams_settings',
            'twitchstreams_templatesection'
        );
        add_settings_field(
            'twitchstreams_streamtemplatedefault',
            'Use default stream template?',
            array("TwitchStreamsSettings", "streamDefaultRenderer"),
            'twitchstreams_settings',
            'twitchstreams_templatesection'
        );
        add_settings_field(
            'twitchstreams_streamtemplate',
            'Stream Template',
            array("TwitchStreamsSettings", "streamTemplateRenderer"),
            'twitchstreams_settings',
            'twitchstreams_templatesection'
        );
        add_settings_field(
            'twitchstreams_useofflinetemplate',
            'Use offline template instead stream template?',
            array("TwitchStreamsSettings", "useOfflineRenderer"),
            'twitchstreams_settings',
            'twitchstreams_templatesection'
        );
        add_settings_field(
            'twitchstreams_offlinetemplatedefault',
            'Use default offline channel template?',
            array("TwitchStreamsSettings", "offlineDefaultRenderer"),
            'twitchstreams_settings',
            'twitchstreams_templatesection'
        );
        add_settings_field(
            'twitchstreams_offlinetemplate',
            'Offline Channel Template',
            array("TwitchStreamsSettings", "offlineTemplateRenderer"),
            'twitchstreams_settings',
            'twitchstreams_templatesection'
        );
    }

    static function twitchSectionRenderer(){
        // echo "<p>Twitch Stream Introduction</p>";
    }

    static function templateSectionRenderer(){

    }

    // Twitch input renderers
    static function showOfflineRenderer(){
        $setting = get_option('twitchstreams_showoffline');
        ?>
        <input name="twitchstreams_showoffline" type="checkbox" id="twitchstreams_showoffline" <?php if($setting) echo "checked" ?> value="true">
        <?php
    }

    static function streamsCacheRenderer(){
        $setting = get_option('twitchstreams_streams_cache');
        ?>
        <input type="number" class="regular-text" name="twitchstreams_streams_cache" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
        <?php
    }

    static function twitchTokenRenderer(){
        $setting = get_option('twitchstreams_twitch_token');
        ?>
        <input type="text" class="regular-text" name="twitchstreams_twitch_token" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
        <?php
    }

    static function channelsRenderer(){
        $setting = get_option('twitchstreams_channels');
        ?>
        <input type="text" class="regular-text" name="twitchstreams_channels" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
        <?php
    }

    static function limitRenderer(){
        $setting = get_option('twitchstreams_limit');
        ?>
        <input type="number" class="regular-text" name="twitchstreams_limit" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
        <?php
    }

    // Template input renderers
    static function streamTemplateRenderer(){
        $setting = get_option('twitchstreams_streamtemplate');
        ?>
        <textarea rows="10" spellcheck="false" class="regular-text" name="twitchstreams_streamtemplate"><?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?></textarea>
        <?php
    }
    static function mainTemplateRenderer(){
        $setting = get_option('twitchstreams_maintemplate');
        ?>
        <textarea rows="10" spellcheck="false" class="regular-text" name="twitchstreams_maintemplate"><?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?></textarea>
        <?php
    }
    static function mainDefaultRenderer(){
        $setting = get_option('twitchstreams_maintemplatedefault');
        ?>
        <input name="twitchstreams_maintemplatedefault" type="checkbox" id="twitchstreams_maintemplatedefault" <?php if($setting) echo "checked" ?> value="true">
        <?php
    }
    static function streamDefaultRenderer(){
        $setting = get_option('twitchstreams_streamtemplatedefault');
        ?>
        <input name="twitchstreams_streamtemplatedefault" type="checkbox" id="twitchstreams_streamtemplatedefault" <?php if($setting) echo "checked" ?> value="true">
        <?php
    }
    static function useOfflineRenderer(){
        $setting = get_option('twitchstreams_useofflinetemplate');
        ?>
        <input name="twitchstreams_useofflinetemplate" type="checkbox" id="twitchstreams_useofflinetemplate" <?php if($setting) echo "checked" ?> value="true">
        <?php
    }
    static function offlineDefaultRenderer(){
        $setting = get_option('twitchstreams_offlinetemplatedefault');
        ?>
        <input name="twitchstreams_offlinetemplatedefault" type="checkbox" id="twitchstreams_offlinetemplatedefault" <?php if($setting) echo "checked" ?> value="true">
        <?php
    }
    static function offlineTemplateRenderer(){
        $setting = get_option('twitchstreams_offlinetemplate');
        ?>
        <textarea rows="10" spellcheck="false" class="regular-text" name="twitchstreams_offlinetemplate"><?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?></textarea>
        <?php
    }
}


?>