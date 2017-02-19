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
			$raw_iframe_code = arv_get_raw_iframe_code( $video_url );
			$instance['video_source'] = arv_get_iframe_attribute( $raw_iframe_code , 'src' );
			$instance['aspect_ratio_class'] = arv_get_class_for_aspect_ratio( $raw_iframe_code );
			$instance['video_url'] = esc_url( $video_url );
		}
		return $instance;
	}

	public function widget( $args, $instance ) {
		$video_source = isset( $instance['video_source'] ) ? $instance['video_source'] : '';
		$aspect_ratio_class = isset( $instance['aspect_ratio_class'] ) ? $instance['aspect_ratio_class'] : '';
		if ( $video_source ) {
			$bootstrap_responsive_video = get_bootstrap_responsive_video( $video_source , $aspect_ratio_class );
			echo $args['before_widget'] . $bootstrap_responsive_video . $args['after_widget'];
		}
	}

	function arv_get_raw_iframe_code( $url ) {
		$raw_code = wp_oembed_get( esc_url( $url ) );
		return $raw_code;
	}

}
