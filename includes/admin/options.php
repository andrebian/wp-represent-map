<?php
/**
 * Options page
 * 
 * @since 1.0.0
 */


/**
 * Management for options
 * 
 * @since 1.0.0
 */
function manage_options_for_wp_represent_map()
{
    if ( isset($_POST['_wp_represent_map_default_city']) ) {
        
        $option_data = array(
            '_wp_represent_map_default_city' => $_POST['_wp_represent_map_default_city'],
            '_wp_represent_map_default_lat_lng' => $_POST['_wp_represent_map_default_lat_lng'],
        );
        
        if ( update_option('wp-represent-map', $option_data) ) {
            echo '<br /><div class="update-nag">'.__('Options saved with success', 'wp-represent-map').'</div>';
        } else {
            echo '<br /><div class="update-nag">'.__('No changes made', 'wp-represent-map').'</div>';
        }
    }
    
    $options_values = get_option('wp-represent-map');
    ?>

    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br></div>
        <h2><?php echo __('Wp Represent Map Settings', 'wp-represent-map'); ?></h2>

        <form name="form" action="" method="post">
            <p><?php echo __('Change your location and another stuffs', 'wp-represent-map'); ?></p>

            <h3><?php echo __('Settings', 'wp-represent-map'); ?></h3>
            <table class="form-table permalink-structure" style="width: 40%; float: left;">
                <tbody>
                    <tr>
                        <th>
                            <label>
                                <?php echo __('Default City', 'wp-represent-map'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="_wp_represent_map_default_city" value="<?php echo @$options_values['_wp_represent_map_default_city']; ?>">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label>
                                <?php echo __('Default Lat Lng', 'wp-represent-map'); ?>
                                &nbsp;
                                <a href="#" onclick="return false" title="<?php echo __('Lat and Long is need to determine the center of the map on default screen', 'wp-represent-map'); ?>">
                                    <strong>?</strong>
                                </a>
                            </label>
                        </th>
                        <td>
                            <input type="text" name="_wp_represent_map_default_lat_lng" value="<?php echo @$options_values['_wp_represent_map_default_lat_lng']; ?>">&nbsp;
                            <a href="#" title="<?php echo __('How I discover Lat Lng?', 'wp-represent-map'); ?>" id="ShowTipLatLng">
                                <img src="../wp-content/plugins/wp-represent-map/assets/img/info.png">
                            </a>
                        </td>
                    </tr>
                    <tr id="TipLatLng" style="display:none;">
                        <td colspan="2">
                            <div class="update-nag" style="border-radius: 5px; padding:10px;">
                                <?php echo __('Go at http://maps.google.com.br and follow these steps <br />1: type your location, browse to center map where you want<br />2: at the options click in a chain icon, browse in the link has open at his side, <br />copy the values like the step 3', 'wp-represent-map'); ?>
                                <br />
                                <img src="../wp-content/plugins/wp-represent-map/assets/img/map-lat-lng.png">
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
            
            <br clear="all">
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save Changes', 'wp-represent-map'); ?>">
            </p>  
        </form>
    </div>

<script>
    jQuery(document).ready(function($) {
        $("#ShowTipLatLng").click(function() {
            $("#TipLatLng").toggle("slow");
            return false;
        });
    });
</script>
    <?php
}