<?php
add_action( 'init', 'fgsmm_fitsoftbtn_buttons' );

 /**
*Add button filters
*/
function fgsmm_fitsoftbtn_buttons() {
	add_filter("mce_external_plugins", "fgsmm_fitsoftbtn_add_buttons");
    add_filter('mce_buttons', 'fgsmm_fitsoftbtn_register_buttons');
}	

/**
*Return the url of button plugin js
*/
function fgsmm_fitsoftbtn_add_buttons($plugin_array) {
	$plugin_array['fgsmmbtn'] = plugins_url( 'fitsoftbtn-plugin.js', __FILE__ );
	return $plugin_array;
}
/**
*Register the button types
*/
function fgsmm_fitsoftbtn_register_buttons($buttons) {
	array_push( $buttons, 'fsclassschedule', 'fscalendar', 'fsmemberlogin', 'fssignup' );
	return $buttons;
}

?>