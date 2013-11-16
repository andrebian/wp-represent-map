<?php

function meta_box_item_map_info()
{ 
    $post = func_get_arg(0);
    ?>
    <label><?php echo __('Address', 'wp-represent-map'); ?></label>
    <br />
    <input type="text" id="_wp_represent_map_address" name="_wp_represent_map_address" 
           value="<?php echo get_post_meta($post->ID, '_wp_represent_map_address', true); ?>"
           style="width: 99%;" >
    
    <br /><br />
    <label><?php echo __('LatLng', 'wp-represent-map'); ?></label>
    <br />
    <input type="text" id="_wp_represent_map_lat_lng" name="_wp_represent_map_lat_lng" 
           value="<?php echo get_post_meta($post->ID, '_wp_represent_map_lat_lng', true); ?>"
           style="width: 99%;" >
    
    <div id="feedback"></div>
    <script>
        jQuery(document).ready(function($) {
           $("#_wp_represent_map_address").blur(function(){
              $.ajax({
                type: "POST",
                url: "<?php echo get_admin_url(); ?>admin-ajax.php?action=get_lat_lng",
                data: "address="+$(this).val(),
                success: function(html) {
                  $("#_wp_represent_map_lat_lng").val(html);
                }
              }); 
           }); 
        });
    </script>
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