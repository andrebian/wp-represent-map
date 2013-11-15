<?php
/*
Plugin Name: WP Represent Map
Plugin URI: 
Description: Possibilita o cadastro e manutenção de diversoso tipos de itens em mapas.
Version: 1.0.0
Author: Andre Cardoso aka andrebian
Author URI: http://andrebian.com
License: GPLv2
*/

require 'includes/load.php';

add_action('admin_menu', 'wp_represent_map_options_page');
function wp_represent_map_options_page() 
{
        add_options_page( 'WP Represent Map', 'WP Represent Map', 7, __FILE__, 'manage_options_for_wp_represent_map');
}

/* Translation files */
load_plugin_textdomain( 'wp-represent-map', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
