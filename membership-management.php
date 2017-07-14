<?php
/*
 Plugin Name: Gym Studio Membership Management
 Plugin URI: http://news.fitsoft.com/wordpress-plugin-setup
 Description: Add calendar, schedule of classes, membership checkout, and members login to your posts or pages. Manage Members using Gym Studio Membership Management System. Now includes an optional floating widget for all pages. For installation instruction visit <a href="http://news.fitsoft.com/wordpress-plugin-setup" target="_blank">Setup Instruction</a>. 
 Version: 1.0.4
 Author: Fitsoft
 Author URI: http://software.fitsoft.com
 Text Domain: fitsoft-membership-management
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define('FGSMM_SERVICE_VIEW_URL', "https://admin.fitsoft.com/PluginFrames/Wordpress/view.aspx");
define('FGSMM_SERVICE_HANDLER', "https://admin.fitsoft.com/handlers/wordpresshandler.ashx");

/*Application ID identifying the request is from Wordpress. Public Identifier. */
define('FGSMM_APP_UID', "8efb2019-456e-4ee8-b7ef-1ca704e95213"); 

register_activation_hook( __FILE__, 'fgsmm_member_create_db' );
define('FGSMM__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('FGSMM__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
*create the fitsoft code database for the embedcode
*/
function fgsmm_member_create_db() {

	global $wpdb;
  	$version = get_option( 'fitsoft_membership_plugin_version', '0.0.1' );
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'fitsoft';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		embedcode text NOT NULL,
		embedname varchar(250) NOT NULL,
		embeddescription varchar(500) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

require_once (FGSMM__PLUGIN_DIR.'/membership-management-common.php');
if (is_admin()){ 
	require_once (FGSMM__PLUGIN_DIR.'/membership-management-options.php');
}
if (is_admin()){ 
	require_once (FGSMM__PLUGIN_DIR.'/membership-management-buttons.php');
}


add_shortcode('fitsoft-code', 'fgsmm_fitsoft_embedcode');

/**
*Return membership management shortcodes
*/
function fgsmm_fitsoft_embedcode($atts)
{
	extract( shortcode_atts( array(
	'control' => 'class',
	), $atts ) );
	
	$html = " <script type='text/javascript' id='fitsoft_widget' src='https://admin.fitsoft.com/js/IFrameWidget2.js'></script>";
	
	$dictionary = array("login" => "login",
				  "calendar" => "calendar",
				  "class" => "classes",
				  "signup" => "isignup");
	
	if (array_key_exists($control, $dictionary)) {
		$rows = fgsmm_getEmbedCodeInformation($dictionary[$control]);
		if(count($rows) > 0)
		{
			foreach($rows as $row){
				$html .= base64_decode($row->embedcode);
				break;
			}
		}
	 
	}
	
	return $html;
}

add_action( 'wp_enqueue_scripts', 'fgsmm_load_scripts' );

/**
 * Add Shortcode JS Script to frontend
 */
function fgsmm_load_scripts() {
	wp_enqueue_style( 'fgsmm1.6.3ColorBoxStyle', 'https://admin.fitsoft.com/PluginFrames/Wordpress/css/hoverpop.css' );
	wp_enqueue_script( 'fgsmm1.0.2_script', 'https://admin.fitsoft.com/js/IFrameWidget2.js', array('jquery'), '1.0.2');
	wp_enqueue_script( 'fgsmm1.6.3Colorbox', 'https://admin.fitsoft.com/PluginFrames/Wordpress/js/jquery.hoverwcolorbox.js', array('jquery'), '1.6.3', true);	
}

/**
*Return embedcode information
*/
function fgsmm_getEmbedCodeInformation($emedcodename)
{
	global $wpdb;
	$embedcodename = trim($embedcodename);
	$table_name = $wpdb->prefix . 'fitsoft';
	$searchfor = preg_replace("/[^a-zA-Z0-9]+/", "", $emedcodename);
	$sql = "Select * from ". $table_name." where embedname = '".$searchfor."';";
	$res = $wpdb->get_results($sql);
	return $res;
}

/**
* Add footer if user set it to display
*/
function fgsmm_footer() {
     
	$options = get_option( 'my_fitsoft_options' );
	$fshoverpop = isset( $options['fshoverpop']) ? esc_attr( $options['fshoverpop']) : '';
	if($fshoverpop == '1') print fgsmm_outputHoverMenu();
	 
}

add_action('wp_footer', 'fgsmm_footer');

?>