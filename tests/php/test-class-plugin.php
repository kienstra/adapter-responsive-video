<?php
/**
 * Tests for class Plugin.
 *
 * @package AdapterResponsiveVideo
 */

namespace AdapterResponsiveVideo;

/**
 * Tests for class Plugin.
 */
class Test_Plugin extends \WP_UnitTestCase {

	/**
	 * Instance of plugin.
	 *
	 * @var Plugin
	 */
	public $plugin;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		wp_maybe_load_widgets();
		$this->plugin = Plugin::get_instance();
	}

	/**
	 * Test get_instance().
	 *
	 * @covers Plugin::get_instance().
	 */
	public function test_get_instance() {
		$this->assertEquals( Plugin::get_instance(), $this->plugin );
		$this->assertEquals( __NAMESPACE__ . '\Plugin', get_class( Plugin::get_instance() ) );

		// Ensure that get_instance() instantiates Plugin correctly when Plugin::$instance is null.
		Plugin::$instance = null;
		$instance         = Plugin::get_instance();
		$this->assertEquals( Plugin::$instance, $instance );
	}

	/**
	 * Test init().
	 *
	 * @covers Plugin::init().
	 */
	public function test_init() {
		$this->plugin->init();
		$this->assertTrue( class_exists( __NAMESPACE__ . '\\' . Plugin::WIDGET_CLASS ) );
		$this->assertEquals( 10, has_action( 'init', array( $this->plugin, 'plugin_localization' ) ) );
		$this->assertEquals( 10, has_action( 'widgets_init', array( $this->plugin, 'register_widget' ) ) );
	}

	/**
	 * Test plugin_localization().
	 *
	 * @covers Plugin::plugin_localization().
	 */
	public function test_plugin_localization() {
		$this->plugin->plugin_localization();
		$this->assertNotEquals( false, did_action( 'load_textdomain' ) );
	}

	/**
	 * Test register_widget().
	 *
	 * @covers Plugin::register_widget().
	 */
	public function test_register_widget() {
		global $wp_widget_factory;
		$this->plugin->register_widget();
		$this->assertTrue( isset( $wp_widget_factory->widgets[ __NAMESPACE__ . '\\' . Plugin::WIDGET_CLASS ] ) );
	}
}
