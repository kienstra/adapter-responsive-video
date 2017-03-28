<?php
/**
 * Class Adapter_Responsive_Video_Plugin
 *
 * @package AdapterResponsiveVideo
 */

namespace AdapterResponsiveVideo;

/**
 * Class Adapter_Responsive_Video_Plugin
 *
 * @package AdapterResponsiveVideo
 */
class Adapter_Responsive_Video_Plugin {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $plugin_slug = 'adapter-responsive-video';

	/**
	 * Construct the class.
	 */
	public function __construct() {
		require_once dirname( __FILE__ ) . '/class-adapter-responsive-video.php';
		add_action( 'init' , array( $this, 'plugin_localization' ) );
		add_action( 'widgets_init' , array( $this, 'register_widget' ) );
	}

	/**
	 * Load the textdomain for the plugin, enabling translation.
	 *
	 * @return void.
	 */
	public function plugin_localization() {
		load_plugin_textdomain( $this->plugin_slug, false, $this->plugin_slug . '/languages' );
	}

	/**
	 * Register the Adapter Responsive Video widget.
	 *
	 * @return void.
	 */
	public function register_widget() {
		register_widget( 'AdapterResponsiveVideo\Adapter_Responsive_Video' );
	}

}
