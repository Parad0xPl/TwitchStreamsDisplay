<?php

if(!function_exists("do_action")){
    echo("<!-- plugin ->");
    exit;
}

require_once("display.php");

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
			'render_callback' => array("TwitchStreams_Display", "renderer"),
		)
	);
}

add_action( 'init', 'twitchStreamRegisterBlock' );

?>