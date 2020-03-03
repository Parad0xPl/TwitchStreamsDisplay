<?php

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}

require_once("twitch_api.php");

class TwitchStreams_Block {

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
		$twitch = TwitchStreams_TwitchAPI::get();
		
		$template = self::mainTemplate;
		
		$streams = get_transient(self::cache_key);
		if($streams === false){
			$channels = explode(",", get_option("twitchstreams_channels"));
			$response = $twitch->streams($channels);
			if($response === null){
				return "Can't get streams";
			}
			$streams = $response["data"];
			set_transient(self::cache_key, $streams, 10);
		}
        
        return sprintf($template, print_r($streams ,TRUE), self::renderStreams($streams));
    }
}

// function twitchStreamRegisterWidget() {
// 	register_widget('TwitchStreams_Widget');
// }
// add_action( 'widgets_init', 'twitchStreamRegisterWidget' );

/**
 * Registers the `core/calendar` block on server.
 */
function twitchStreamRegisterBlock() {

	$asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
 
    wp_register_script(
        'twitchstreams-blockscript',
        plugins_url( 'build/index.js', __FILE__ ),
        $asset_file['dependencies'],
        $asset_file['version']
    );

	wp_register_style(
		"twitchstreams-mainstyle",
		plugins_url( 'css/style.css', __FILE__ ),
		array(),
        filemtime( plugin_dir_path( __FILE__ ) . 'css/style.css' )
	);

	register_block_type(
		'twitchstreams/display',
		array(
			'attributes'      => array(
				'align'     => array(
					'type' => 'string',
					'enum' => array( 'left', 'center', 'right', 'wide', 'full' ),
				),
				'className' => array(
					'type' => 'string',
				)
			),
			"style" => "twitchstreams-mainstyle",
			"editor_script" => 'twitchstreams-blockscript',
			'render_callback' => array("TwitchStreams_Block", "renderer"),
		)
	);
}

add_action( 'init', 'twitchStreamRegisterBlock' );

?>