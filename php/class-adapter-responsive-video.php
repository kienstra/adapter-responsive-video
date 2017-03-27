<?php
/**
 * Class Adapter_Responsive_Video
 *
 * @package AdapterResponsiveVideo
 */

namespace AdapterResponsiveVideo;

/**
 * Class Adapter_Responsive_Video
 *
 * @package AdapterResponsiveVideo
 */
class Adapter_Responsive_Video extends \WP_Widget {

	/**
	 * Instantiate the widget class.
	 */
	public function __construct() {
		$options = array(
			'classname' => 'adapter-responsive-video',
			'description' => __( 'Video from YouTube, Vimeo, and more.' , 'adapter-responsive-video' ),
		);
		parent::__construct( 'adapter_responsive_video' , __( 'Adapter Video' , 'adapter-responsive-video' ) , $options );
	}

	/**
	 * Output the widget form.
	 *
	 * @param array $instance Widget data.
	 * @return void.
	 */
	public function form( $instance ) {
		$video_url = isset( $instance['video_url'] ) ? $instance['video_url'] : '';
		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'video_url' ) ); ?>">
					<?php esc_html_e( 'Video url' , 'adapter-responsive-video' ); ?>
				</label>
				<input type="text" value="<?php echo esc_url( $video_url ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'video_url' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'video_url' ) ); ?>" placeholder="e.g. www.youtube.com/watch?v=mOXRZ0eYSA0" \>
			</p>
		<?php
	}

	/**
	 * Update the widget instance, based on the form submission.
	 *
	 * @param array $new_instance New widget data, updated from form.
	 * @param array $previous_instance Widget data, before being updated from form.
	 * @return array $instance Widget data, updated based on form submission.
	 */
	public function update( $new_instance, $previous_instance ) {
		$instance = $previous_instance;
		$video_url = isset( $new_instance['video_url'] ) ? $new_instance['video_url'] : '';
		if ( $video_url ) {
			$raw_iframe_code = $this->get_raw_iframe_code( $video_url );
			$instance['video_source'] = $this->get_iframe_attribute( $raw_iframe_code , 'src' );
			$instance['aspect_ratio_class'] = $this->get_class_for_aspect_ratio( $raw_iframe_code );
			$instance['video_url'] = esc_url( $video_url );
		}
		return $instance;
	}

	/**
	 * Echo the markup of the widget.
	 *
	 * @param array $args Widget display data.
	 * @param array $instance Data for widget.
	 * @return void.
	 */
	public function widget( $args, $instance ) {
		$video_source = isset( $instance['video_source'] ) ? $instance['video_source'] : '';
		$aspect_ratio_class = isset( $instance['aspect_ratio_class'] ) ? $instance['aspect_ratio_class'] : '';
		if ( $video_source ) {
			$bootstrap_responsive_video = $this->get_markup( $video_source , $aspect_ratio_class );
			echo wp_kses_post( $args['before_widget'] ) . $bootstrap_responsive_video . wp_kses_post( $args['after_widget'] );
		}
	}

	/**
	 * Get the iframe code, in order to later output it on the widget.
	 *
	 * @param string $url To use in order to get the iframe markup.
	 * @return false|string
	 */
	public function get_raw_iframe_code( $url ) {
		$raw_code = wp_oembed_get( esc_url( $url ) );
		return $raw_code;
	}

	/**
	 *
	 * Get the full markup of a Bootstrap responsive video container
	 *
	 * @param string $src Value to output in src attribute.
	 * @param string $class Value to output in class attribute.
	 * @return string $video_container Full markup of a responsive video container.
	 */
	public function get_markup( $src, $class ) {
		$max_width = apply_filters( 'arv_video_max_width' , '580' );
		return '<div class="responsive-video-container" style="max-width:' . esc_attr( $max_width ) . 'px">
					<div class="embed-responsive ' . esc_attr( $class ) . '">
						<iframe class="embed-responsive-item" src="' . esc_url( $src ) . '">
						</iframe>
					 </div>
				</div>';
	}

	/**
	 * Get the value of a specific attribute in the iframe.
	 *
	 * @param string $iframe Markup of iframe.
	 * @param string $attribute In the iframe, search for this attribute's value.
	 * @return string $value Corresponds to the passed attribute.
	 */
	public function get_iframe_attribute( $iframe, $attribute ) {
		$pattern	= '/<iframe.*?' . $attribute . '=\"([^\"]+?)\"/';
		preg_match( $pattern , $iframe , $matches );
		if ( isset( $matches[1] ) ) {
			return $matches[1];
		}
	}

	/**
	 * Get the Bootstrap class, given a width-to-height aspect ratio.
	 *
	 * @param string $embed_code The code to be output in the Boostrap container.
	 * @return string $bootstrap_class The specific class for this aspect ratio.
	 */
	public function get_class_for_aspect_ratio( $embed_code ) {
		$prefix = 'embed-responsive-';
		$aspect_ratio = self::get_raw_aspect_ratio( $embed_code );
		if ( self::is_ratio_closer_to_four_by_three( $aspect_ratio ) ) {
			return $prefix . '4by3';
		} else {
			return $prefix . '16by9';
		}
	}

	/**
	 * Get numeric aspect ratio of the embed markup.
	 *
	 * @param string $embed_code Markup, from which to find the aspect ratio.
	 * @return float|false $aspect_ratio Width-to-height ratio of video, or false if these values are missing.
	 */
	public function get_raw_aspect_ratio( $embed_code ) {
		$embed_width = self::get_iframe_attribute( $embed_code , 'width' );
		$embed_height = self::get_iframe_attribute( $embed_code , 'height' );
		if ( $embed_width && $embed_height ) {
			$aspect_ratio = floatval( $embed_width ) / floatval( $embed_height );
			return $aspect_ratio;
		} else {
			return false;
		}
	}

	/**
	 * Whether the aspect ratio is closer to four by three than sixteen by nine.
	 *
	 * @param float $ratio Width-to-height aspect ratio.
	 * @return bool $is_closer Whether the ratio is closer to four by three.
	 */
	public function is_ratio_closer_to_four_by_three( $ratio ) {
		$difference_from_four_by_three = self::get_difference_from_four_by_three( $ratio );
		$difference_from_sixteen_by_nine = self::get_difference_from_sixteen_by_nine( $ratio );
		return ( $difference_from_four_by_three < $difference_from_sixteen_by_nine );
	}

	/**
	 * Get the numeric difference between the aspect ratio and a four by three ratio.
	 *
	 * @param float $value To compare to the ratio.
	 * @return float $difference Numeric difference between the $value and $four_by_three.
	 */
	public function get_difference_from_four_by_three( $value ) {
		$four_by_three = 1.3333;
		$difference_from_four_by_three = abs( $value - $four_by_three );
		return $difference_from_four_by_three;
	}

	/**
	 * Get the numeric difference between the aspect ratio and a sixteen by nine ratio.
	 *
	 * @param float $value To compare to the ratio.
	 * @return float $difference Numeric difference between the $value and $sixteen_by_nine.
	 */
	public function get_difference_from_sixteen_by_nine( $value ) {
		$sixteen_by_nine = 1.777;
		$difference_from_sixteen_by_nine = abs( $value - $sixteen_by_nine );
		return $difference_from_sixteen_by_nine;
	}

}
