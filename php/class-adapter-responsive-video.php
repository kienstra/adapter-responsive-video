<?php

class Adapter_Responsive_Video extends WP_Widget {

	public function __construct() {
		$options = array(
			'classname' => 'adapter-responsive-video',
			'description' => __( 'Video from YouTube, Vimeo, and more.' , 'adapter-responsive-video' ),
		);
		 $this->WP_Widget( 'adapter_responsive_video' , __( 'Adapter Video' , 'adapter-responsive-video' ) , $options );
	}

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

	public function widget( $args, $instance ) {
		$video_source = isset( $instance['video_source'] ) ? $instance['video_source'] : '';
		$aspect_ratio_class = isset( $instance['aspect_ratio_class'] ) ? $instance['aspect_ratio_class'] : '';
		if ( $video_source ) {
			$bootstrap_responsive_video = $this->get( $video_source , $aspect_ratio_class );
			echo $args['before_widget'] . $bootstrap_responsive_video . $args['after_widget'];
		}
	}

	public function get_raw_iframe_code( $url ) {
		$raw_code = wp_oembed_get( esc_url( $url ) );
		return $raw_code;
	}

	public function get( $src, $class ) {
		$max_width = apply_filters( 'arv_video_max_width' , '580' );
		return '<div class="responsive-video-container" style="max-width:' . esc_attr( $max_width ) . 'px">
					<div class="embed-responsive ' . esc_attr( $class ) . '">
						<iframe class="embed-responsive-item" src="' . esc_url( $src ) . '">
						</iframe>
					 </div>
				</div>';
	}

	public function get_iframe_attribute( $iframe, $attribute ) {
		$pattern	= '/<iframe.*?' . $attribute . '=\"([^\"]+?)\"/';
		preg_match( $pattern , $iframe , $matches );
		if ( isset( $matches[1] ) ) {
			return $matches[1];
		}
	}

	public function get_class_for_aspect_ratio( $embed_code ) {
		$bootstrap_apect_ratio = self::get_bootstrap_aspect_ratio( $embed_code );
		return 'embed-responsive-' . $bootstrap_apect_ratio;
	}

	public function get_bootstrap_aspect_ratio( $embed_code ) {
		$aspect_ratio = self::get_raw_aspect_ratio( $embed_code );
		if ( self::is_ratio_closer_to_four_by_three( $aspect_ratio ) ) {
			return '4by3';
		} else {
			return '16by9';
		}
	}

	public function get_raw_aspect_ratio( $embed_code ) {
		$embed_width = self::get_iframe_attribute( $embed_code , 'width' );
		$embed_height = self::get_iframe_attribute( $embed_code , 'height' );
		if ( $embed_width && $embed_height ) {
			$aspect_ratio = floatval( $embed_width ) / floatval( $embed_height );
			return $aspect_ratio;
		}
	}

	function is_ratio_closer_to_four_by_three( $ratio ) {
		$difference_from_four_by_three = self::get_difference_from_four_by_three( $ratio );
		$difference_from_sixteen_by_nine = self::get_difference_from_sixteen_by_nine( $ratio );
		return ( $difference_from_four_by_three < $difference_from_sixteen_by_nine );
	}

	function get_difference_from_four_by_three( $value ) {
		$four_by_three = 1.3333;
		$difference_from_four_by_three = abs( $value - $four_by_three );
		return $difference_from_four_by_three;
	}

	public function get_difference_from_sixteen_by_nine( $value ) {
		$sixteen_by_nine = 1.777;
		$difference_from_sixteen_by_nine = abs( $value - $sixteen_by_nine );
		return $difference_from_sixteen_by_nine;
	}

}
