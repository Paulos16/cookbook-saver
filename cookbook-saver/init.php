<?php

// We need the ABSPATH
if (!defined('ABSPATH')) exit;

define('COOKBOOK_SAVER_VERSION', '0.1.0');
define('COOKBOOK_SAVER_DIR', dirname(COOKBOOK_SAVER_FILE));
define('COOKBOOK_SAVER_SLUG', 'cookbook-saver');

register_activation_hook(COOKBOOK_SAVER_FILE, 'cookbook_saver_activation');
register_deactivation_hook(COOKBOOK_SAVER_FILE, 'cookbook_saver_deactivation');

function cookbook_saver_activation() {
	// global $wpdb;

	add_option('cookbook_saver_version', COOKBOOK_SAVER_VERSION);
    add_option('cookbook_saver_options', array());
    add_shortcode('cookbook-saver-button', 'parse_shortcode');
}

function cookbook_saver_deactivation() {
    return;
}

function cookbook_saver_update_check() {
    //global $wpdb;
    
    $sql = array();
    $current_version = get_option('cookbook_saver_version');
    $version = (int) str_replace('.', '', $current_version);

    // No update required
    if($current_version == COOKBOOK_SAVER_VERSION){
        return true;
    }

    // Is it first run ?
    if(empty($current_version)){

        // Reinstall
        cookbook_saver_activation();

        // Trick the following if conditions to not run
        $version = (int) str_replace('.', '', COOKBOOK_SAVER_VERSION);

    }

    // Save the new Version
    update_option('cookbook_saver_version', COOKBOOK_SAVER_VERSION);
}

function parse_shortcode() {
    return '';
}

function export_cookbook() {
    //
}
