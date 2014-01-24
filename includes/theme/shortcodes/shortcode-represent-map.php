<?php
/**
 * Shows the represent map
 * 
 * @since 1.0.0
 */

$dir = '';
if ( is_admin() ) {
    $dir = '.';
}

require $dir . './wp-content/plugins/' . PLUGIN_DIR_NAME . '/classes/RepresentMap.php';

/**
 * Shows the default represent map
 * following the type provided by pass
 * 
 * 
 * @param string $type
 * 
 * @since 1.0.0
 */
function represent_map( $type = null )
{
    $rm = new RepresentMap();
    
    $all_map_items = $rm->setType( $type );
    $args = $rm->getArgs();
//    print_r($args);
//    
//    die();
    $posts = query_posts($args);
    
    /**
     * Overriden $type 
     */
    $type = $rm->getType();
    
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
    
    $blog_uri = get_bloginfo('url');
    $url_base = $blog_uri . '/wp-content/uploads/map-icons/';
    
    $options = get_option('wp-represent-map');
    
    $lat_lng = $options['_wp_represent_map_default_lat_lng'];
    $height_map = (true === $all_map_items) ? ALL_HEIGHT_MAP : SINGLE_HEIGHT_MAP;
    $width_map = (true === $all_map_items) ? '80%' : '100%';
    
    require './wp-content/plugins/wp-represent-map/includes/theme/content/map.php';
    
}



function _map_type( $type ) 
{
    if ( !empty($type) ) {
        $type = $type['type'];
        
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
        $all_map_items = true;
    }
}


add_shortcode('represent-map', 'represent_map');
