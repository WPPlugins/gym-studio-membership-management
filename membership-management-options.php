<?php

//error_reporting(0);
if( is_admin() ) $fgsmm_my_settings_page = new fgsmm_MySettingsPage();
 /**
 * Class handling the simple setting page for entering password and username
 * Password will not be saved
 */
class fgsmm_MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
	private $varCurlEnabled;
	private $varGetFileEnabled;
    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
		
		$this->varGetFileEnabled = ini_get('allow_url_fopen') ? true : false;
		$this->varCurlEnabled = function_exists('curl_version') ? true : false;

		if(!$this->varGetFileEnabled && !$this->varCurlEnabled) add_action('admin_notices', array( $this, 'my_admin_notice' ));
    }
	
	function my_admin_notice(){
	    echo '<div class="updated">
	       <p>Required: Curl or allow_url_fopen not enabled. Please enabled one of these.</p>
	    </div>';
	}

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_options_page(
            'Settings Admin', 
            'Membership Management', 
            'manage_options', 
            'my-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'my_fitsoft_options' );
        ?>
        <div class="wrap">
            <h2>Fitsoft Membership Management Login</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_fitsoft_group' );   
                do_settings_sections( 'my-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_fitsoft_group', // Option group
            'my_fitsoft_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'fssetting_section_id', // ID
            'Access Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );  
		
		add_settings_field(
            'emailaddress', 
            'Email Address', 
            array( $this, 'emailaddress_callback' ), 
            'my-setting-admin', 
            'fssetting_section_id'
        ); 

        add_settings_field(
            'fspassword', // ID
            'Password', // Title 
            array( $this, 'fspassword_callback' ), // Callback
            'my-setting-admin', // Page
            'fssetting_section_id' // Section           
        ); 
		
		add_settings_section(
            'fssetting_showpopup_id', // ID
            'Membership Management Hovering Menu (Bottom Right Corner)', // Title
            array( $this, 'print_sectionpopup_info' ), // Callback
            'my-setting-admin' // Page
        );  
		
		
		add_settings_field(
            'fshoverpop', // ID
            'Add Hover Menu', // Title 
            array( $this, 'hoverup_callback' ), // Callback
            'my-setting-admin', // Page
            'fssetting_showpopup_id' // Section           
        ); 


		
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['fspassword'] ) )
			$new_input['fspassword'] = sanitize_text_field( $input['fspassword'] );

        if( isset( $input['emailaddress'] ) )
            $new_input['emailaddress'] = sanitize_text_field( $input['emailaddress'] );
		
		if( isset( $input['fshoverpop'] ) )
            $new_input['fshoverpop'] = sanitize_text_field( $input['fshoverpop'] );

        return $new_input;
    }


	
    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'After signing up you can find your Fitsoft username and password in your email inbox.<br/>Enter your credential below:';
    }
	
	    /** 
     * Print the Section text
     */
    public function print_sectionpopup_info()
    {
        print 'Easily add signups, classes, calendar, and login to the lower right hand corner of your website without creating new pages:';
    }
	
	
	// Display and fill the form field
	public function hoverup_callback() {
	
		if(isset( $this->options['fshoverpop'] ))
		{
			//$this->saveAccessKeys($this->options['emailaddress'],"");
			$fs_options = array(
				"emailaddress" => $this->options['emailaddress'],
				"fspassword" => "",
				"fshoverpop" => $this->options['fshoverpop']
			);
			update_option( 'my_fitsoft_options', $fs_options );		

			if($this->options['fshoverpop'] == '1') 
			{
				if(get_option("fitsoft_livekey") <> false && get_option("fitsoft_accesskey") <> false && !empty($this->options['emailaddress']))
				{
						$this->fgsmm_savepreviewurl(base64_decode(get_option("fitsoft_livekey")), base64_decode(get_option("fitsoft_accesskey")), $this->options['emailaddress']);
					
				}
			}
		}
		printf(
            '<input id="fshoverpop" name="my_fitsoft_options[fshoverpop]" type="checkbox" value="%s" %s/>','1', ($this->options['fshoverpop'] == '1') ? "checked" : ""
        );
	}

    /** 
     * Get the settings option array and print one of its values
     */
    public function fspassword_callback()
    {
        printf(
            '<input id="fspassword" name="my_fitsoft_options[fspassword]" type="password" value="%s" />',
            isset( $this->options['fspassword'] ) ? esc_attr( $this->options['fspassword']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function emailaddress_callback()
    {
		if(isset( $this->options['fspassword'] ) && isset( $this->options['emailaddress'] ) )
		{
			$this->saveAccessKeys($this->options['emailaddress'],$this->options['fspassword']);
			$fs_options = array(
				"emailaddress" => $this->options['emailaddress'],
				"fspassword" => "",
				"fshoverpop" => $this->options['fshoverpop']
			);
			update_option( 'my_fitsoft_options', $fs_options );
			sleep(1);
			if(get_option("fitsoft_livekey") <> False && get_option("fitsoft_accesskey") <> False)
			{
				$this->storeEmbedCodes(base64_decode(get_option("fitsoft_livekey")), base64_decode(get_option("fitsoft_accesskey")), $this->options['emailaddress']); //store the embed codes
			}
			
		}
		
        printf(
            '<input type="text" id="emailaddress" name="my_fitsoft_options[emailaddress]" value="%s" />',
            isset( $this->options['emailaddress'] ) ? esc_attr( $this->options['emailaddress']) : ''
        );
    }

	/** 
    * Save the access keys and make sure previewurl is saved
    */
	private function saveAccessKeys($username, $password)
	{
			if(!empty($username) && !empty($password))
			{
				$_url = FGSMM_SERVICE_HANDLER."?username=%s&password=%s&appuid=%s&action=getkeys";	
				$_url = sprintf($_url, $username, $password, FGSMM_APP_UID);

				$contenturl = $this->getUrlContent($_url);	
				if(!empty($contenturl)){
					$data = json_decode($contenturl);
						$livekey = $data->livekey;
						$accesskey = $data->accesskey;
						$previewurl = $data->previewurl;
						if(!empty($livekey) && !empty($accesskey))
						{
							if(get_option("fitsoft_livekey") <> false) update_option('fitsoft_livekey', base64_encode($livekey), '', 'yes'); 
							else add_option( 'fitsoft_livekey', base64_encode($livekey), '', 'yes' ); 
							
							if(get_option("fitsoft_accesskey") <> false) update_option('fitsoft_accesskey', base64_encode($accesskey), '', 'yes'); 
							else add_option( 'fitsoft_accesskey', base64_encode($accesskey), '', 'yes' ); 
						}
						
						if(!empty($previewurl))
						{
							if(get_option("fitsoft_previewurl") <> false) update_option('fitsoft_previewurl', base64_encode($previewurl), '', 'yes'); 
							else add_option( 'fitsoft_previewurl', base64_encode($previewurl), '', 'yes' ); 
							
						}
				}
			}
	}
	
	/** 
    * Save Preview URL
    */
	private function fgsmm_savepreviewurl($livekey, $accesskey, $emailaddress)
	{
		$parsed_service_url = $this->fgsmm_getAccessURL($livekey, $accesskey, $emailaddress);
		if(!empty($parsed_service_url )){
			$_url = $parsed_service_url."&action=getkeys";
			
			$serviceres = $this->getUrlContent($_url);
				
			if(!empty($serviceres)){								
				$data = json_decode($serviceres);
				$previewurl = $data->previewurl;
				
				if(!empty($previewurl))
				{
					if(get_option("fitsoft_previewurl") <> false) update_option('fitsoft_previewurl', base64_encode($previewurl), '', 'yes'); 
					else add_option( 'fitsoft_previewurl', base64_encode($previewurl), '', 'yes' ); 
					
				}
			}
		}
	}
	
	/** 
    * Append URL with access keys
    */
	private function fgsmm_getAccessURL($livekey, $accesskey, $emailaddress)
	{
		if(!empty($livekey) && !empty($accesskey) && !empty($emailaddress))
		{
			$_url = FGSMM_SERVICE_HANDLER."?lk=%s&ak=%s&username=%s&appuid=%s";	
			$_url = sprintf($_url, $livekey, $accesskey, $emailaddress, FGSMM_APP_UID);
		
		}	
		return !empty($_url) ? $_url : FGSMM_SERVICE_HANDLER;
	}


	/** 
    * call the webservice url and get the url embed codes and save them to the database table
    */
	private function storeEmbedCodes($livekey, $accesskey, $username)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'fitsoft';		
		$parsed_service_url = $this->fgsmm_getAccessURL($livekey, $accesskey, $username);
		
		if(!empty($parsed_service_url )){
			$charset_collate = $wpdb->get_charset_collate();
			$_url = $parsed_service_url."&action=getembeds";
			$serviceres = $this->getUrlContent($_url);
			
			if(!empty($serviceres)){
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				
				$wpdb->query("DELETE FROM " . $table_name . "");
						
				$file = json_decode($serviceres);
				foreach($file->codes as $row){
					
					if($this->countembedCodeByName($row->name) < 1){
						$_code = base64_encode($row->code);
						$query = "INSERT INTO " . $table_name . " (time, embedcode, embedname, embeddescription) VALUES ('". date("Y-m-d H:i:s") ."' ,'$_code', '".$row->name."', '".$row->info."')";
						dbDelta( $query );
					}
				}
			}
		}
	}
	
	/** 
    * Return an array with information of the embedcode
    */
	private function fgsmm_getEmbedCodeInformation($emedcodename)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'fitsoft';
		$sql = "Select * from ". $table_name." where embedname = '".$embedcodename."'";
		$res = $wpdb->get_results($sql, ARRAY_N);
		return mysql_fetch_array($res);	
	}

	/** 
    * count the number of embed code
    */
	private function countembedCodeByName($embedcodename)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'fitsoft';
		$sql = "Select * from ". $table_name." where embedname = '".$embedcodename."'";
		$wpdb->get_results($sql);
		return  $wpdb->num_rows;
	}
	
	/** 
    * get json file using curl or fopen
    */
	private function getUrlContent($url){
		if($this->varCurlEnabled){
			try{
				$strarry = explode("?",$url);
				if(count($strarry) > 0){
					$urlwoparams = $strarry[0];
					$parameters = parse_url($url, PHP_URL_QUERY);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $urlwoparams);
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_POST, 1); 
					curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($ch, CURLOPT_CAINFO, FGSMM__PLUGIN_DIR . "/certs/-.fitsoft.crt");
					$data = curl_exec($ch);
					if(curl_errno($ch)){
						//echo 'Curl error: ' . curl_error($ch);
						if($this->varGetFileEnabled) return file_get_contents($url); //use this instead if enabled
					}
					$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					curl_close($ch);
					
					return ($httpcode>=200 && $httpcode<300) ? $data : "";
				}
				
			}
			catch(Exception $ex){
				
				if($this->varGetFileEnabled) return file_get_contents($url);
			}
		}elseif($this->varGetFileEnabled){
			return file_get_contents($url);
		}
		
		return "";
	}
	
}


?>