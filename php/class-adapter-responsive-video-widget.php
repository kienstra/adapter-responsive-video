<?php
/**
 * Class Adapter_Responsive_Video_Widget
 *
 * @package AdapterResponsiveVideo
 */

namespace AdapterResponsiveVideo;

/**
 * Class Adapter_Responsive_Video_Widget
 *
 * @package AdapterResponsiveVideo
 */
class Adapter_Responsive_Video_Widget extends \WP_Widget {

	/**
	 * The ID base of the widget.
	 *
	 * @var string
	 */
	const ID_BASE = 'adapter_responsive_video';

	/**
	 * The class name of the widget.
	 *
	 * @var string
	 */
	const CLASS_NAME = 'adapter-responsive-video';

	/**
	 * The default max width of the div.responsive-video-container.
	 *
	 * @var int
	 */
	const DEFAULT_MAX_WIDTH = 580;

	/**
	 * Instantiates the widget class.
	 */
	public function __construct() {
		parent::__construct(
			self::ID_BASE,
			__( 'Adapter Video', 'adapter-responsive-video' ),
			array(
				'classname'                   => self::CLASS_NAME,
				'customize_selective_refresh' => true,
				'description'                 => __( 'Videos and embeds from YouTube, SlideShare, Spotify, and more.', 'adapter-responsive-video' ),
			)
		);
	}

	/**
	 * Outputs the widget form.
	 *
	 * @param array $instance Widget data.
	 * @return void
	 */
	public function form( $instance ) {
		$video_url = isset( $instance['video_url'] ) ? $instance['video_url'] : '';
		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'video_url' ) ); ?>">
					<?php esc_html_e( 'Video URL', 'adapter-responsive-video' ); ?>
				</label>
				<input type="text" value="<?php echo esc_url( $video_url ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'video_url' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'video_url' ) ); ?>" placeholder="<?php esc_html_e( 'e.g. https://open.spotify.com/track/2XULDEvijLgHttFgLzzpM5', 'adapter-responsive-video' ); ?>" \>
			</p>
		<?php
	}

	/**
	 * Updates the widget instance, based on the form submission.
	 *
	 * @param array $new_instance      New widget data.
	 * @param array $previous_instance Widget data before the form was updated.
	 * @return array $instance Sanitized widget data, updated based on form submission.
	 */
	public function update( $new_instance, $previous_instance ) {
		$instance  = $previous_instance;
		$video_url = isset( $new_instance['video_url'] ) ? esc_url( $new_instance['video_url'] ) : '';
		if ( $video_url ) {
			$instance['video_url']          = esc_url( $video_url );
			$raw_embed_markup               = wp_oembed_get( $video_url );
			$iframe_attributes              = $this->get_iframe_attributes( $raw_embed_markup );
			$instance['video_source']       = $iframe_attributes['src'];
			$instance['aspect_ratio_class'] = $iframe_attributes['class'];
			if ( preg_match( '/^<iframe/', $raw_embed_markup ) ) {
				$instance['iframe'] = str_replace( '<iframe', '<iframe class="embed-responsive-item"', $raw_embed_markup );
			}
		}

		return $instance;
	}

	/**
	 * Echoes the markup of the widget.
	 *
	 * @param array $args Widget display data.
	 * @param array $instance Data for widget.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'] . $this->get_markup( $instance ) . $args['after_widget']; // WPCS: XSS OK.
	}

	/**
	 * Gets the full markup of a Bootstrap responsive embed container.
	 *
	 * @param array $instance The widget instance.
	 * @return string $video_container Full markup of a responsive video container.
	 */
	public function get_markup( $instance ) {
		$video_source       = isset( $instance['video_source'] ) ? $instance['video_source'] : '';
		$aspect_ratio_class = isset( $instance['aspect_ratio_class'] ) ? $instance['aspect_ratio_class'] : '';

		/**
		 * The max-with of the div.responsive-video-container.
		 *
		 * @param int $max_width The max width of the container.
		 */
		$max_width = apply_filters( 'arv_video_max_width', self::DEFAULT_MAX_WIDTH );

		ob_start();
		?>
		<div class="responsive-video-container" style="max-width:<?php echo esc_attr( strval( $max_width ) ); ?>px">
			<div class="embed-responsive <?php echo esc_attr( $aspect_ratio_class ); ?>">
				<?php
				if ( isset( $instance['iframe'] ) ) :
					echo $instance['iframe']; // WPCS: XSS OK.
				else :
					// For backwards compatibility with v1.0.1.
					?>
					<iframe class="embed-responsive-item" src="<?php echo esc_url( $video_source ); ?>"></iframe>
				<?php endif; ?>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Gets the <iframe> aspect ratio class and src.
	 *
	 * The aspect ratio class is based on whether its ratio is closer to 4/3 or 16/9.
	 * It will either be 'embed-responsive-16by9', 'embed-responsive-4by3', or null.
	 * The 'src' is simply the src of the <iframe>, or null if it does not exist.
	 *
	 * @param string $embed_markup The embed markup to search for these attributes.
	 * @return array[] {
	 *     @type string|null $class The aspect ratio class, like 'embed-responsive-16by9'.
	 *     @type string|null $src   The src of the <iframe>.
	 * }
	 */
	public function get_iframe_attributes( $embed_markup ) {
		$libxml_previous_state = libxml_use_internal_errors( true );
		$dom                   = new \DOMDocument( '1.0' );
		$dom->loadHTML( $embed_markup );

		$iframe = $dom->getElementsByTagName( 'iframe' )->item( 0 );
		if ( $iframe ) {
			$width  = $iframe->getAttribute( 'width' );
			$height = $iframe->getAttribute( 'height' );
			$src    = $iframe->getAttribute( 'src' );
		} else {
			$src = null;
		}

		libxml_clear_errors();
		libxml_use_internal_errors( $libxml_previous_state );

		if ( ! empty( $width ) && ! empty( $height ) && is_numeric( $width ) && is_numeric( $height ) ) {
			$ratio        = intval( $width ) / intval( $height );
			$ratio_string = abs( $ratio - 1.3333 ) < abs( $ratio - 1.7777 ) ? '4by3' : '16by9';
			$class        = 'embed-responsive-' . $ratio_string;
		} else {
			$class = null;
		}

		return compact( 'class', 'src' );
	}
}
