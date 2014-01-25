<?php
/**
 * Calls the represent map
 * 
 * @since 0.1
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
 * @since 0.1
 */
function represent_map( $type = null )
{
    $rm = new RepresentMap();
    
    $all_map_items = $rm->setType( $type );
    $args = $rm->getArgs();

    $posts = query_posts($args);
    
    /**
     * Overritten $type 
     */
    $type = $rm->getType();
    
    // Improve a better code bellow
    if ( empty($type) ) {
        $temp_posts = array();
        $cont = 0;
        foreach($posts as $post) {
            
            
            $terms = wp_get_post_terms( $post->ID, 'represent_map_type' );
            $types = _process_terms( $terms );
            
            $temp_posts[$cont] = $post;
            @$temp_posts[$cont]->types = $types;
            
            $cont++;
        }
        $posts = $temp_posts;
    }
    
    $url_base = BLOG_URI . '/wp-content/uploads/map-icons/';
    
    $options = get_option('wp-represent-map');
    
    $lat_lng = $options['_wp_represent_map_default_lat_lng'];
    $height_map = (true === $all_map_items) ? ALL_HEIGHT_MAP : SINGLE_HEIGHT_MAP;
    $width_map = (true === $all_map_items) ? '80%' : '100%';
    
    require './wp-content/plugins/' . PLUGIN_DIR_NAME . '/includes/theme/map.php';
    
}


/**
 * 
 * @param array $terms
 * @return array
 */
function _process_terms( $terms )
{
    $types = array();
    if (!empty($terms)) {
        foreach ($terms as $term) {
            $types[] = $term->slug;
        }
    }
    
    return $types;
}

/**
 * 
 * @param array $type
 */
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
