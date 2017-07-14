<?php

if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}

delete_option('fitsoft_livekey');
delete_option('fitsoft_accesskey');

?>