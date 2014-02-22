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
    
    $categories = get_categories(array(
        'type' => 'represent_map',
        'taxonomy' => 'represent_map_type' 
    ));
    
    
    foreach($categories as $category) {
        if ( 0 != $category->category_parent ) {
            $categories[$category->category_parent]->children[$category->term_id] = $category;
            unset($categories[$category->term_id]);
        }
    }
    
    $posts = get_posts(array(
        'posts_per_page' => 0,
        'offset' => 0,
        'orderby' => 'post_date',
        'order' => 'DESC',
        'include' => '',
        'exclude' => '',
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'represent_map',
        'post_mime_type' => '',
        'post_parent' => '',
        'post_status' => 'publish',
        'suppress_filters' => true,
        'depth' => 1
    ));
    
    $posts_temp = $posts;
    $posts = array();
    foreach( $posts_temp as $post ) {
        $post->address = get_post_meta($post->ID, '_wp_represent_map_address', true);
        $post->lat_lng = get_post_meta($post->ID, '_wp_represent_map_lat_lng', true);
        $post->types = parse_types(get_the_terms( $post->ID, 'represent_map_type' ));
        $posts[] = $post;
    }
    
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
