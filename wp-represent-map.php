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


function type_post_represent_map() {
        $labels = array(
                'name' => __( 'Map items', 'wp-represent-map' ),
                'singular_name' => __( 'Map item', 'wp-represent-map' ),
                'add_new' => __( 'Add new', 'wp-represent-map' ),
                'add_new_item' => __( 'New item', 'wp-represent-map' ),
                'edit_item' => __( 'Edit item', 'wp-represent-map' ),
                'new_item' => __( 'New item', 'wp-represent-map' ),
                'view_item' => __( 'View item', 'wp-represent-map' ),
                'search_items' => __( 'Search items', 'wp-represent-map' ),
                'not_found' => __( 'No registers found', 'wp-represent-map' ),
                'not_found_in_trash' => __( 'No register found in trash', 'wp-represent-map' ),
                'parent_item_colon' => '',
                'menu_name' => __('Map Items', 'wp-represent-map')
        );
        $args = array(
                'labels' => $labels,
                'public' => true,
                'public_queryable' => true,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => true,
                'capability_type' => 'post',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => 5,
                'menu_icon' => plugin_dir_url(__FILE__) . '/assets/img/wp-represent-map.png',
                'register_meta_box_cb' => '',
                'supports' => array( 'title', 'editor', 'custom-fields' )
        );
        register_post_type( 'represent_map', $args );
        
        register_taxonomy(
            'represent_map_type', 'represent_map', array(
                'label' => __('Type', 'wp-represent-map'),
                'singular_label' => __('Type', 'wp-represent-map'),
                'rewrite' => true,
                'hierarchical' => true
                    )
        );

        flush_rewrite_rules();
}
add_action( 'init', 'type_post_represent_map' );


/* Translation files */
load_plugin_textdomain( 'wp-represent-map', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
