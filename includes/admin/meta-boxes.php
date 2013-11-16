<?php

function meta_box_item_map_info()
{ $post = func_get_arg(0);?>
    <label><?php echo __('Address', 'wp-represent-map'); ?></label>
    <br />
    <input type="text" name="_wp_represent_map_address" 
           value="<?php echo get_post_meta($post->ID, '_wp_represent_map_address', true); ?>"
           style="width: 99%;" >
    
    <br /><br />
    <label><?php echo __('LatLng', 'wp-represent-map'); ?></label>
    <br />
    <input type="text" name="_wp_represent_map_lat_lng" 
           value="<?php echo get_post_meta($post->ID, '_wp_represent_map_lat_lng', true); ?>"
           style="width: 99%;" >
<?php 
}


function meta_box_item_map_info_save($post_id)
{
    if ( isset($_POST['_wp_represent_map_address']) ) {
        update_post_meta($post_id, '_wp_represent_map_address', $_POST['_wp_represent_map_address']);
    }
    
    if ( isset($_POST['_wp_represent_map_lat_lng']) ) {
        update_post_meta($post_id, '_wp_represent_map_lat_lng', $_POST['_wp_represent_map_lat_lng']);
    }
}