<?php
/**
 * Shows the default full map
 */


/**
 * Shows the default full map with or without
 * menu and/or lateral menu
 * 
 * @param (optional) boolean $header
 * @param (optional) boolean $lateral_menu
 * 
 * @since 1.0.0
 */
function full_map( $header = true, $lateral_menu = true )
{
    echo 'Full map will appear here';
}

add_shortcode('full-map', 'full_map');