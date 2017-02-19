<?php

function get_bootstrap_responsive_video( $src, $class ) {
	$max_width = apply_filters( 'arv_video_max_width' , '580' );
	return '<div class="responsive-video-container" style="max-width:' . esc_attr( $max_width ) . 'px">
				<div class="embed-responsive ' . esc_attr( $class ) . '">
					<iframe class="embed-responsive-item" src="' . esc_url( $src ) . '">
					</iframe>
				 </div>
			</div>';
}

function arv_get_iframe_attribute( $iframe, $attribute ) {
	$pattern	= '/<iframe.*?' . $attribute . '=\"([^\"]+?)\"/';
	preg_match( $pattern , $iframe , $matches );
	if ( isset( $matches[1] ) ) {
		return $matches[1];
	}
}

function arv_get_class_for_aspect_ratio( $embed_code ) {
	$bootstrap_apect_ratio = get_bootstrap_aspect_ratio( $embed_code );
	return 'embed-responsive-' . $bootstrap_apect_ratio;
}

function get_bootstrap_aspect_ratio( $embed_code ) {
	$aspect_ratio = arv_get_raw_aspect_ratio( $embed_code );
	if ( is_ratio_closer_to_four_by_three( $aspect_ratio ) ) {
		return '4by3';
	} else {
		return '16by9';
	}
}

function arv_get_raw_aspect_ratio( $embed_code ) {
	$embed_width = arv_get_iframe_attribute( $embed_code , 'width' );
	$embed_height = arv_get_iframe_attribute( $embed_code , 'height' );
	if ( $embed_width && $embed_height ) {
		$aspect_ratio = floatval( $embed_width ) / floatval( $embed_height );
		return $aspect_ratio;
	}
}

function is_ratio_closer_to_four_by_three( $ratio ) {
	$difference_from_four_by_three = get_difference_from_four_by_three( $ratio );
	$difference_from_sixteen_by_nine = get_difference_from_sixteen_by_nine( $ratio );
	return ( $difference_from_four_by_three < $difference_from_sixteen_by_nine );
}

function get_difference_from_four_by_three( $value ) {
	$four_by_three = 1.3333;
	$difference_from_four_by_three = abs( $value - $four_by_three );
	return $difference_from_four_by_three;
}

function get_difference_from_sixteen_by_nine( $value ) {
	$sixteen_by_nine = 1.777;
	$difference_from_sixteen_by_nine = abs( $value - $sixteen_by_nine );
	return $difference_from_sixteen_by_nine;
}