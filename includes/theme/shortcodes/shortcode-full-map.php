<?php
/**
 * Shows the default full map
 */


/**
 * Shows the default full map with or without
 * menu and/or lateral menu
 * 
 * @param (optional) boolean $header
 * @param (optional) boolean $menu
 * 
 * @since 1.0.0
 */
function full_map()
{
    $passed_args = func_get_args(); 
    $top_bar = $passed_args[0]['top_bar'] === 'true' ? true : false;
    $menu = $passed_args[0]['menu'] === 'true' ? true : false;
   
    // getting total items for map
    $markers = array();
    if ( $terms = get_terms('represent_map_type') ) {
        foreach($terms as $term) {
            $markers[$term->slug] = array(
                'total' => (int) $term->count,
                'name' => $term->name,
                'slug' => $term->slug
            );
        }
    }
    
    $item_to_map = array();
    $items = get_posts('post_type=represent_map&posts_per_page=10000');
    
    $types = function( $terms ){
        $term_list = array();
        if ( !empty($terms) ) {
            foreach( $terms as $term ) {
                $term_list[$term->slug] = $term->slug;
            }
            
        }
        return $term_list;
    };
    
    foreach( $items as $item ) {
        $item_to_map[] = array(
            'name' => $item->post_title,
            'slug' => $item->post_name,
            'description' => $item->content,
            'type' => $types(wp_get_post_terms($item->ID, 'represent_map_type'))
        );
    }
    
    $url_base = get_bloginfo('url') . '/wp-content/plugins/' . PLUGIN_DIR_NAME . '/includes/theme/content/';
    $options = get_option('wp-represent-map');
    
    $lat_lng = $options['_wp_represent_map_default_lat_lng'];

    require './wp-content/plugins/wp-represent-map/includes/theme/content/index.php';
    if ( $top_bar ) {
        require './wp-content/plugins/wp-represent-map/includes/theme/content/header-map.php';
    }
    
    if ( $menu ) {
        require './wp-content/plugins/wp-represent-map/includes/theme/content/header.php';
    }
    
}

add_shortcode('full-map', 'full_map');