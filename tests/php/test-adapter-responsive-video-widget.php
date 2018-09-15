<?php
/**
 * Tests for class Adapter_Responsive_Video_Widget.
 *
 * @package AdapterResponsiveVideo
 */

namespace AdapterResponsiveVideo;

/**
 * Tests for class Adapter_Responsive_Video_Widget.
 */
class Test_Adapter_Responsive_Video_Widget extends \WP_UnitTestCase {

	/**
	 * Instance of widget.
	 *
	 * @var Adapter_Responsive_Video_Widget
	 */
	public $widget;

	/**
	 * A mock video URL.
	 *
	 * @var string
	 */
	const MOCK_VIDEO_URL = 'https://youtu.be/XOY3ZUO6P0k';

	/**
	 * The expected aspect ratio class.
	 *
	 * @var string
	 */
	const EXPECTED_ASPECT_RATIO_CLASS = 'embed-responsive-16by9';

	/**
	 * The expected embed <iframe> src value, returned from wp_oembed_get().
	 *
	 * @var string
	 */
	const EXPECTED_EMBED_IFRAME_SRC = 'https://www.youtube.com/embed/XOY3ZUO6P0k?feature=oembed';

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->widget = new Adapter_Responsive_Video_Widget();
	}

	/**
	 * Test construct.
	 *
	 * @covers Adapter_Responsive_Video_Widget::__construct()
	 */
	public function test_construct() {
		$this->assertEquals(
			array(
				'classname'                   => Adapter_Responsive_Video_Widget::CLASS_NAME,
				'customize_selective_refresh' => true,
				'description'                 => 'Video from YouTube, Vimeo, and more.',
			),
			$this->widget->widget_options
		);
		$this->assertEquals( Adapter_Responsive_Video_Widget::ID_BASE, $this->widget->id_base );
		$this->assertEquals( 'Adapter Video', $this->widget->name );
	}

	/**
	 * Test form.
	 *
	 * @covers Adapter_Responsive_Video_Widget::form()
	 */
	public function test_form() {
		// If there is no 'video_url' in the argument, the <input> should have an empty value.
		ob_start();
		$this->widget->form( array() );
		$this->assertContains( 'value=""', ob_get_clean() );

		// If there is a 'video_url' in the argument, it should be output in the form.
		ob_start();
		$this->widget->form( array( 'video_url' => self::MOCK_VIDEO_URL ) );
		$output = ob_get_clean();
		$this->assertContains( '<input type="text" value="' . self::MOCK_VIDEO_URL, $output );
		$this->assertContains( 'Video URL', $output );
	}

	/**
	 * Test update.
	 *
	 * @covers Adapter_Responsive_Video_Widget::update()
	 */
	public function test_update() {
		// If only a wrong key is in the $new_instance, the instance should not be updated.
		$invalid_value = 'Foo';
		$new_instance  = $this->widget->update(
			array(
				'unexpected_key' => $invalid_value,
			),
			array()
		);
		$this->assertEquals( array(), $new_instance );

		// There is a 'video_url' in the instance, so it should be updated.
		$new_instance = $this->widget->update(
			array(
				'video_url' => self::MOCK_VIDEO_URL,
			),
			array()
		);

		// The $new_instance should have all of the values in the $expected_instance, and it should have an 'iframe' value that begins with <iframe.
		$expected_instance = array(
			'aspect_ratio_class' => self::EXPECTED_ASPECT_RATIO_CLASS,
			'video_url'          => self::MOCK_VIDEO_URL,
		);
		$this->assertEmpty( array_diff( $expected_instance, $new_instance ) );
		$this->assertContains( '<iframe class="embed-responsive-item"', $new_instance['iframe'] );
	}

	/**
	 * Test widget.
	 *
	 * @covers Adapter_Responsive_Video_Widget::widget()
	 */
	public function test_widget() {
		global $post;
		$post          = $this->factory()->post->create_and_get(); // WPCS: global override ok.
		$before_widget = '<section>';
		$after_widget  = '</section>';
		$args          = compact( 'before_widget', 'after_widget' );
		$instance      = array(
			'video_source'       => self::MOCK_VIDEO_URL,
			'aspect_ratio_class' => self::EXPECTED_ASPECT_RATIO_CLASS,
		);

		ob_start();
		$this->widget->widget( $args, $instance );
		$output = ob_get_clean();
		$this->assertContains( $before_widget, $output );
		$this->assertContains( $after_widget, $output );
		$this->assertContains( '<div class="responsive-video-container"', $output );
		$this->assertContains( self::EXPECTED_ASPECT_RATIO_CLASS, $output );
		$this->assertContains( self::MOCK_VIDEO_URL, $output );
	}

	/**
	 * Test get_markup.
	 *
	 * @covers Adapter_Responsive_Video_Widget::get_markup()
	 */
	public function test_get_markup() {
		$markup = $this->widget->get_markup(
			array(
				'video_source'       => self::MOCK_VIDEO_URL,
				'aspect_ratio_class' => self::EXPECTED_ASPECT_RATIO_CLASS,
			)
		);

		$this->assertContains( self::MOCK_VIDEO_URL, $markup );
		$this->assertContains( self::EXPECTED_ASPECT_RATIO_CLASS, $markup );
		$this->assertContains( strval( Adapter_Responsive_Video_Widget::DEFAULT_MAX_WIDTH ), $markup );
		$this->assertContains( '<iframe class="embed-responsive-item"', $markup );
	}

	/**
	 * Test get_iframe_attributes.
	 *
	 * @covers Adapter_Responsive_Video_Widget::get_iframe_attributes()
	 */
	public function test_get_iframe_attributes() {
		$mock_iframe         = wp_oembed_get( self::MOCK_VIDEO_URL );
		$expected_attributes = array(
			'class' => self::EXPECTED_ASPECT_RATIO_CLASS,
			'src'   => self::EXPECTED_EMBED_IFRAME_SRC,
		);
		$this->assertEquals(
			$expected_attributes,
			$this->widget->get_iframe_attributes( $mock_iframe )
		);

		// If an <iframe> is nested in an element, this should still get its attributes.
		$this->assertEquals(
			$expected_attributes,
			$this->widget->get_iframe_attributes( sprintf( '<div class="foobar">%s</div>', $mock_iframe ) )
		);

		// If embed markup is passed that does not have an <iframe>, both values should be null.
		$this->assertEquals(
			array(
				'class' => null,
				'src'   => null,
			),
			$this->widget->get_iframe_attributes( '<div class="foobar"></div>' )
		);
	}
}
