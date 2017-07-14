<?php 
add_action('admin_menu', 'fgsmm_fitsoft_plugin_setup_menu');
if (function_exists('register_deactivation_hook'))
	register_deactivation_hook(__FILE__, 'fgsmm_fitsoft_deinstall');

 /**
 * Delete options in database
 */
function fgsmm_fitsoft_deinstall() {
	if(get_option("fitsoft_livekey") <> False) delete_option('fitsoft_livekey');
	if(get_option("fitsoft_accesskey") <> False) delete_option('fitsoft_accesskey');
	if(get_option("fitsoft_previewurl") <> False) delete_option('fitsoft_previewurl');
}

/**
* Add the page for backend membership management
*/
function fgsmm_fitsoft_plugin_setup_menu(){
        add_menu_page( 'Membership Management', 'Membership Management', 'manage_options', 'membership_management_plugin', 'fgsmm_plugin_init' );
}

/**
* Start the plugin
*/
function fgsmm_plugin_init(){
	$livekey = (get_option("fitsoft_livekey") <> False ) ? (base64_decode(get_option("fitsoft_livekey"))) : "";
	$accesskey = (get_option("fitsoft_accesskey") <> False) ? (base64_decode(get_option("fitsoft_accesskey"))) : "";
	$options = get_option( 'my_fitsoft_options' );
	$emailaddress = isset( $options['emailaddress']) ? esc_attr( $options['emailaddress']) : '';
    echo fgsmm_outputManagementPage($livekey, $accesskey, $emailaddress);
}

function fgsmm_outputHoverMenu()
{
	$res = "";
	if(get_option("fitsoft_previewurl") <> False){
		$previewurl = str_replace("http://", "https://", base64_decode(get_option("fitsoft_previewurl")));
		$servicedir = "https://admin.fitsoft.com/PluginFrames/Wordpress/images/hoverpop";
		$res = "<div class='fitsoft92ie-wrapper'>
		<div class='fitsoft92ie'>
			<ul>
				<li>
					<a href='$previewurl/signup' class='fgsmmiframe'>
						<div><img src='$servicedir/join-wh1.png' /></div>
						<div>Join</div> 
					</a>
				</li>
				<li>
					<a href='$previewurl/classes' class='fgsmmiframe'>
						<div><img src='$servicedir/checkin.png' /></div>
						<div>Classes</div>
					</a>
				</li>
				<li>
					<a href='$previewurl/calendar' class='fgsmmiframe'>
						<div><img src='$servicedir/calendar-wh1.png' /></div>
						<div>Calendar</div>
					</a>
				</li>			
				<li>
					<a href='$previewurl/login' class='fgsmm-smiframe'>
						<div><img src='$servicedir/member-wh1.png' /></div>
						<div>Login</div>
					</a>
				</li>
			</ul>
		</div>
		</div>";
	}
	
	return $res;
	
}


/**
* Link to the signup or management page.
* No livekey and accesskey then we out the signup button.
*/
function fgsmm_outputManagementPage($livekey, $accesskey, $emailaddress)
{
	$newservicevieweurl = fgsmm_getAccessURL($livekey, $accesskey, $emailaddress);
	
	$imagename = "accountbtn.png";
	$pagetitle = "Create Your Account";
	$pageinfo  = "Click on the button below to create your account.";
	$installationinfo = fgsmm_installation_info();
	if(get_option("fitsoft_livekey") <> False && get_option("fitsoft_accesskey") <> False)
	{
	
		$imagename = "cpbtn.png";
		$pagetitle = "Membership Management";
		$pageinfo  = "Click on the button below to go to the membership management page.";
		$installationinfo = "";
	}
	
	$resurlfromserviewparse = "<div class='wrap'>
									<h2>$pagetitle</h2>
									<p>$pageinfo</p>
									<p><a href='$newservicevieweurl' target='_blank'><img src='".FGSMM__PLUGIN_URL."/images/$imagename'></a></p>
									$installationinfo
								</div>";

	$res = sprintf($resurlfromserviewparse,$newservicevieweurl);
	return $res;
}

function fgsmm_installation_info()
{
	return "<div style='margin-top:20px;'>
			<h2>Step by step:</h2>
			<p>More on installation: <a href='http://news.fitsoft.com/wordpress-plugin-setup' target='blank'>Installation Tutorial</a></p>
			<ul>
				<li><h3>1. Click on 'Create Account' button & create your account</h3></li>
				<li><h3>2. Check your inbox for email confirmation link</h3></li>
				<li><h3>3. Click on the email link and complete account information to receive your plugin password email</h3></li>
				<li><h3>4. Copy and paste plugin password to your Wordpress 'Setting' -> 'Membership Management' page (Password is auto-generated & emailed to you)</h3></li>
				<li><h3>5. Now you can start using the calendar, schedule of classes, and login shortcode buttons. Find more info here: <a href='http://news.fitsoft.com/more-on-wordpress' target='_blank'> more info.</a> </h3></li>

			</ul>
			</div>";
}

/**
* append the keys to service url for request.
*/
function fgsmm_getAccessURL($livekey, $accesskey, $emailaddress)
{
	if(!empty($livekey) && !empty($accesskey) && !empty($emailaddress))
	{
		$_url = FGSMM_SERVICE_VIEW_URL."?lk=%s&ak=%s&username=%s&appuid=%s";	
		$_url = sprintf($_url, $livekey, $accesskey, $emailaddress, FGSMM_APP_UID);
	}	
	return !empty($_url) ? $_url : FGSMM_SERVICE_VIEW_URL;
}

?>