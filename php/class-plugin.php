<?php
/**
 * Class Plugin
 *
 * @package AdapterResponsiveVideo
 */

namespace AdapterResponsiveVideo;

/**
 * Class Plugin
 *
 * @package AdapterResponsiveVideo
 */
class Plugin {

	/**
	 * The class name of the widget.
	 *
	 * @var string
	 */
	const WIDGET_CLASS = 'Adapter_Responsive_Video_Widget';

	/**
	 * The instance of this class.
	 *
	 * @var Plugin
	 */
	public static $instance;

	/**
	 * Gets the instance of this plugin.
	 *
	 * @return Plugin $instance The plugin instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof Plugin ) {
			self::$instance = new Plugin();
		}
		return self::$instance;
	}

	/**
	 * Construct the class.
	 */
	public function init() {
		require_once dirname( __FILE__ ) . '/class-adapter-responsive-video-widget.php';
		add_action( 'init', array( $this, 'plugin_localization' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
	}

	/**
	 * Load the textdomain for the plugin, enabling translation.
	 *
	 * @return void.
	 */
	public function plugin_localization() {
		load_plugin_textdomain( 'adapter-responsive-video', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register the Adapter Responsive Video widget.
	 *
	 * @return void.
	 */
	public function register_widget() {
		register_widget( __NAMESPACE__ . '\\' . self::WIDGET_CLASS );
	}
}
