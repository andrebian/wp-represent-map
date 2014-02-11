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
    
            
            $terms = get_categories(array(
                        'type' => 'represent_map',
                        'taxonomy' => 'represent_map_type')
                    );
                    
            $categories = array();
            if (!empty($terms)) {
                foreach ($terms as $t) {
                    if (0 == $t->parent) {
                        $categories[$t->term_id] = $t;
                        
                        // Obtendo os posts
                        $categories[$t->term_id]->posts = get_posts_by_category($t->slug);
                        unset($terms[$t->term_id]);
                    }
                }
            }
            
            foreach($terms as $cat) {
                if ( !empty($cat->name) && !empty($cat->parent) ) {
                    //Obtendo os posts
                    $cat->posts = get_posts_by_category($t->slug);
                    
                    $categories[$cat->parent]->children[] = $cat;
                }
            }
    
            print_r($categories);
    die();
    
    if ( !empty($posts) ) :
            foreach($posts as $post) :
        die(var_dump($post));
                
                if ( isset($post->types[0]) ) {
                    $icon_type = $post->types[0];
                } else {
                    @$icon_type = $type;
                }
                
                if ( empty($icon_type) ) {
                    $icon_type = 'default';
                }
                
                $lat_lng = explode(',',get_post_meta($post->ID, '_wp_represent_map_lat_lng', true));
                $lat = $lat_lng[0];
                $lng = $lat_lng[1];
                
                echo "markers.push(['".$post->post_title."', '".$icon_type."', '".$lat."', '".$lng."', '".$post->post_title."', '".$post->post_title."', '".get_post_meta($post->ID, '_wp_represent_map_address', true)."']);";
                
            endforeach;
        endif;
    
    
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
