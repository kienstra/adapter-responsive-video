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

require_once dirname( __FILE__ ) . '/php/class-adapter-responsive-video-plugin.php';

global $adapter_responsive_video_plugin;
$adapter_responsive_video_plugin = new Adapter_Responsive_Video_Plugin();
