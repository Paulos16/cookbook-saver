<?php
/**
 * Plugin Name: Cookbook Saver
 * Plugin URI: https://github.com/Paulos16/cookbook-saver
 * Description: Extension plugin for Cooked letting you download your favorited recipes as a .csv file.
 * Version: 0.1.0
 * Author: Paulos16
 * Author URI: https://github.com/Paulos16/
 */

// if (!defined('ABSPATH')) exit;

// if(!function_exists('add_action')){
// 	echo 'You are not allowed to access this page directly.';
// 	exit;
// }

// $_tmp_plugins = get_option('active_plugins');

// if(!in_array('cooked/cooked.php', $_tmp_plugins) || !in_array('cooked-addon/cooked-pro.php', $_tmp_plugins)) {
// 	return;
// }

// if(defined('COOKBOOK_SAVER_VERSION')) {
// 	return;
// }

// define('COOKBOOK_SAVER_FILE', __FILE__);

// include_once(dirname(__FILE__).'/init.php');

add_action('wp_enqueue_scripts', 'enqueue_cookbook_saver_scripts');
function enqueue_cookbook_saver_scripts() {
		wp_enqueue_script(
			'cookbook-saver-script',
			get_site_url(null, 'wp-content/plugins/cookbook-saver/js/cookbook-saver.js'),
			array('jquery')
		);
		wp_localize_script(
			'cookbook-saver-script',
			'cookbook',
			array('ajax_url' => admin_url('admin-ajax.php'))
		);
}

add_shortcode('cookbook-saver-button', 'parse_shortcode');
add_action('wp_ajax_download_cookbook', 'download_cookbook');
// add_action('wp_ajax_nopriv_download_cookbook', 'download_cookbook');

function parse_shortcode() {
	
	if (get_current_user_id() == 0 || !endsWith($_SERVER['REQUEST_URI'], 'cooked_pn=favorites'))
		return '';
		
	$html = '<button onclick="requestCookbookDownload()">Save your favorited recipes as a .csv file</button>';

	return $html;
}

function download_cookbook() {
	$csv_file_content = get_cookbook_for_user(get_current_user_id());
	
	if (is_null($csv_file_content))
		echo 'Could not generate .csv file.';
	
	echo $csv_file_content;
	
	wp_die();
}

function get_cookbook_for_user($user_id) {
	$user_data = get_user_meta($user_id, 'cooked_user_meta', true);

	if (!isset($user_data) || count($user_data) <= 0)
		return null;

	$cookbook_recipes = $user_data[favorites];

	$cookbook = 'title,description,difficulty_level,prep_time,cook_time,total_time,ingredients(amount|measurement|name),directions';
	$cookbook .= "\n";
	foreach($cookbook_recipes as $recipe) {
		$recipe_data = get_post_meta($recipe, '_recipe_settings', true);

		$cookbook .= $recipe_data[post_title] . ',';
		$cookbook .= $recipe_data[excerpt] . ',';
		$cookbook .= $recipe_data[difficulty_level] . ',';
		$cookbook .= $recipe_data[prep_time] . ',';
		$cookbook .= $recipe_data[cook_time] . ',';
		$cookbook .= $recipe_data[total_time] . ',';
		foreach($recipe_data[ingredients] as $ingredient) {
			$cookbook .= $ingredient[amount] . '|' . $ingredient[measurement] . '|' . $ingredient[name] . ';';
		}
		$cookbook = substr($cookbook, 0, strlen($cookbook)-1);
		$cookbook .= ',';
		foreach($recipe_data[directions] as $direction) {
			$cookbook .= $direction[content] . ';';
		}
		$cookbook = substr($cookbook, 0, strlen($cookbook)-1);
		$cookbook .= "\n";
	}
	$cookbook = substr($cookbook, 0, strlen($cookbook)-1);
	
	return $cookbook;
}

function endsWith( $haystack, $needle ) {
	$length = strlen( $needle );
	if( !$length ) {
			return true;
	}
	return substr( $haystack, -$length ) === $needle;
}
