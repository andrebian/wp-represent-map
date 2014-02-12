<?php
/**
 * Calls the represent map
 * 
 * @since 0.1
 */

require plugin_dir_path( dirname( dirname( __FILE__ ) ) ) .  'classes/RepresentMap.php';

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
    
    $categories = get_categories_and_posts();
    //print_r($categories);
     
    $categories_and_posts = remove_child_posts_from_parent($categories);
    
    $posts = parse_posts_from_all_categories( $categories_and_posts );
    /*
    var_dump('----------------------------------------------------');
    var_dump('----------------------------------------------------');
    print_r($categories);
    die();
    */
    $options = get_option('wp-represent-map');
    
    $lat_lng = $options['_wp_represent_map_default_lat_lng'];
    $height_map = (true === $all_map_items) ? ALL_HEIGHT_MAP : SINGLE_HEIGHT_MAP;
    $width_map = (true === $all_map_items) ? '80%' : '100%';
    
    require plugin_dir_path( dirname( dirname( __FILE__ ) ) ) .  'includes/theme/map.php';
    
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
