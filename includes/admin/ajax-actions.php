<?php
/**
 * Groups all ajax actions from WP Represent Map
 * 
 * @since 0.1
 */


/**
 * Takes the latitude and logintude from an address
 * 
 * @since 0.1
 */
function get_lat_lng()
{
    $url_vendor_map = 'http://maps.googleapis.com/maps/api/geocode/json';
    
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    
    $address_details = json_decode(file_get_contents($url_vendor_map . '?address=' . urlencode($address) . '&sensor=false'), true);
    $lat_lng_prepare = $address_details['results'][0]['geometry']['location'];
    
    $lat_lng = implode(',', $lat_lng_prepare);
    echo $lat_lng;
    exit;
}