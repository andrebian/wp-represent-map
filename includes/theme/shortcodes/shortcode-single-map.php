<?php
/**
 * Shows the single map
 * 
 * @since 1.0.0
 */


/**
 * Shows the defaul single map
 * following the type provided by pass
 * 
 * 
 * @param string $type
 * 
 * @since 1.0.0
 */
function single_map()
{
    $passed_args = func_get_args(); 
    $type = isset($passed_args[0]['type']) ? $passed_args[0]['type'] : '';
    
    if ( !empty($type) ) {
        $args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'represent_map_type',
                    'field' => 'name',
                    'terms' => $type
                )
            )
        );
    } else {
        $args = array(
            'post_type' => 'represent_map'
        );
    }
    $posts = query_posts($args);
    
    
    // Improve a better code bellow
    if ( empty($type) ) {
        $temp_posts = array();
        $cont = 0;
        foreach($posts as $post) {
            $types = array();
            if ( $terms = wp_get_post_terms( $post->ID, 'represent_map_type' ) ) {
                foreach($terms as $term) {
                    $types[] = $term->slug;
                }
            }
            $temp_posts[$cont] = $post;
            @$temp_posts[$cont]->types = $types;
            
            $cont++;
        }
        $posts = $temp_posts;
    }
    
    $url_base = get_bloginfo('url') . '/wp-content/plugins/' . PLUGIN_DIR_NAME . '/includes/theme/content/';
    $options = get_option('wp-represent-map');
    
    $lat_lng = $options['_wp_represent_map_default_lat_lng'];
    $height_map = '400px';
    
    require './wp-content/plugins/wp-represent-map/includes/theme/content/map.php';
    
}

add_shortcode('single-map', 'single_map');