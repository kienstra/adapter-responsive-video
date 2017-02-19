<?php

class Adapter_Responsive_Video_Plugin {

	public function __construct() {
		require_once dirname( __FILE__ ) . '/php/class-adapter-responsive-video.php';
		add_action( 'init' , array( $this, 'plugin_localization' ) );
		add_action( 'widgets_init' , array( $this, 'register_widget' ) );
	}

	public function plugin_localization() {
		load_plugin_textdomain( 'adapter-responsive-video' , false , basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public function register_widget() {
		register_widget( 'Adapter_Responsive_Video' );
	}

}

