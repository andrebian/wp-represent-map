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
function single_map( $type = null )
{
    echo 'The single map will appear here';
}

add_shortcode('single-map', 'single_map');