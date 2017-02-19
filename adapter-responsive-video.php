<?php

/*
Plugin Name: Adapter Responsive Video
Plugin URI: www.ryankienstra.com/responsive-video
Description: A video widget that fits any screen size. Also makes all videos in posts resize to the screen. To get started, go to "Appearance" > "Widgets" and create an "Adapter Video" widget.
Version: 1.0.0
Author: Ryan Kienstra
Author URI: www.ryankienstra.com
License: GPLv2
*/

add_action( 'init' , 'arv_localization_callback' );
function arv_localization_callback() {
	load_plugin_textdomain( 'adapter-responsive-video' , false , basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action( 'widgets_init' , 'arv_register_widget' );
function arv_register_widget() {
	register_widget( 'Adapter_Responsive_Video' );
}

require_once dirname( __FILE__ ) . '/php/class-adapter-responsive-video.php';
