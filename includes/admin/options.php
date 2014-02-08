<?php
/**
 * Options page
 * 
 * @since 0.1
 */

/**
 * Management for options
 * 
 * @since 0.1
 */
function manage_options_for_wp_represent_map()
{
    $errors = array();
    $upload = new Upload();
        
    $wp_upload_dir = wp_upload_dir();
    $upload->setBasePath( $wp_upload_dir['basedir'] . '/map-icons' );
    $upload->appendAllowedType('image/png');
    
    
    if ( isset($_POST) ) {
        
        if ( isset($_POST['_wp_represent_map_default_city']) ) {

            $wp_represent_map_default_city = filter_input(INPUT_POST, '_wp_represent_map_default_city', FILTER_SANITIZE_STRING);
            $wp_represent_map_default_lat_lng = filter_input(INPUT_POST, '_wp_represent_map_default_lat_lng', FILTER_SANITIZE_STRING);
            
            $option_data = array(
                '_wp_represent_map_default_city' => $wp_represent_map_default_city,
                '_wp_represent_map_default_lat_lng' => $wp_represent_map_default_lat_lng,
            );

            if ( update_option('wp-represent-map', $option_data) ) {
                $_SESSION['message'] = __('Options saved with success', 'wp-represent-map');
            } else {
                $_SESSION['message'] = __('No changes made', 'wp-represent-map');
            }
            
            wp_redirect( admin_url() .'options-general.php?page=wp-represent-map/wp-represent-map.php' );
            exit;
        }
        
        
        if ( isset($_FILES) && !empty($_FILES) ) {
            $filename = filter_input(INPUT_POST, 'map_type', FILTER_SANITIZE_STRING);
            $_FILES['pin']['name'] = $filename;
            
            $upload->prepareUpload( $_FILES['pin'] )->flush();
            $errors = $upload->getErrors();
            
            
            if ( empty($errors) ) {
                $_SESSION['message'] = __('Pin uploaded with success', 'wp-represent-map');
            } else {
                $_SESSION['message'] = __(sprintf('Fail to upload the file. Info: %s', implode(', ', $errors)), 'wp-represent-map');
            }
            
            wp_redirect( admin_url() .'options-general.php?page=wp-represent-map/wp-represent-map.php&tab=markers' );
            exit;
        }
    }
    
    if ( isset($_GET['delete']) && !empty($_GET['delete']) ) {
        $delete = base64_decode(filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_STRING));
        $upload->removeFile( $delete . '.png' );
        
        $removeErrors = $upload->getErrors();
        if ( empty($errors) ) {
            $errors = $removeErrors;
        } else {
            array_push($errors, $removeErrors);
        }
        
        if( empty($removeErrors) ) {
            $_SESSION['message'] = __('Pin removed with success', 'wp-represent-map');
        } else {
            $_SESSION['message'] = __(sprintf('Fail to delete Pin. Info: $s', implode($removeErrors)), 'wp-represent-map');
        }
        
        wp_redirect( admin_url() .'options-general.php?page=wp-represent-map/wp-represent-map.php&tab=markers' );
        exit;
    }

    if( !empty($errors) ) {
        $errors = implode('<br />', $errors);
    }
    
    $options_values = get_option('wp-represent-map');
    ?>

    <link rel="stylesheet" href="<?php echo plugins_url('assets/css/represent-map.css', dirname( dirname( __FILE__) ) ); ?>" />

    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br></div>
        <h2><?php echo __('Wp Represent Map Settings', 'wp-represent-map'); ?></h2>

        <?php if ( !empty($errors) ) : ?>
            <br />
            <div class="update-nag">
                <?php echo $errors; ?>
            </div>
        <?php endif; ?>
            
            <?php if ($_SESSION['message']): ?>
                <div class="message updated">
                    <p><?php echo $_SESSION['message'] ?></p>
                </div>
                <?php
                $_SESSION['message'] = false;
            endif;
            ?>    
            
        <div class="page-content">
            <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="#" id="positioning-click" class="nav-tab nav-tab-active">
                    <?php echo __('Default coordenates', 'wp-represent-map'); ?>
                </a>
                <a href="#" id="markers-click" class="nav-tab ">
                    <?php echo __('Markers', 'wp-represent-map'); ?>
                </a>
            </h2>
        
            <div id="positioning">
                <form name="form" action="" method="post">
                    <p><?php echo __('Change your location and another stuffs', 'wp-represent-map'); ?></p>

                    <h3><?php echo __('Settings', 'wp-represent-map'); ?></h3>
                    <table class="form-table permalink-structure permalink-structure-wp-represent-map">
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
                            <tr id="TipLatLng">
                                <td colspan="2">
                                    <div class="update-nag update-nag-wp-represent-map">
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
                
            <div id="markers">
                
                <?php 
                    $icons = array();
                    
                    $path = opendir('../wp-content/uploads/map-icons');
                    while( $file = readdir( $path ) ) {
                        if ( '.' != $file && '..' != $file ) {
                            $icons[$file] = $file;
                        } 
                    }
                    closedir($path);
                    
                    $terms = get_categories(array(
                        'type' => 'represent_map',
                        'taxonomy' => 'represent_map_type')
                    );
                    
                    $categories = array();
                    if (!empty($terms)) {
                        foreach ($terms as $t) {
                            if (0 == $t->parent) {
                                $categories[$t->term_id] = $t;
                                $categories[$t->term_id]->childs = get_categories(array(
                                    'type' => 'represent_map',
                                    'taxonomy' => 'represent_map_type',
                                    'children_of' => $t->term_id
                                ));
                            }
                        }
                    }
    ?>
                
                <form action="" name="markers" method="post" enctype="multipart/form-data">
                    <h3><?php echo __('Create or update a pin', 'wp-represent-map'); ?></h3>
                    
                    <?php echo __('Link to: ', 'wp-represent-map'); ?>&nbsp;
                    <select name="map_type">
                        <option value="default.png"><?php echo __('Default', 'wp-represent-map'); ?></option>
                        <?php 
                            
                            if ( !empty($categories) ) : ?>
                                <?php foreach( $categories as $category ) : ?>
                                    <?php 
                                        
                                    ?>
                                    <option value="<?php echo $category->slug; ?>.png"><?php echo $category->name; ?></option>
                                    
                                    <?php if ( !empty($category->childs) ) : ?>
                                        <?php foreach($category->childs as $child) : ?>
                                            <option value="<?php echo $category->slug . '-' . $child->slug; ?>.png">
                                                &nbsp;&nbsp;&nbsp;
                                                <?php echo $child->name; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                    </select>
                    <input type="file" name="pin" >&nbsp;
                    <input type="submit" class="submit-marker button-primary" value="<?php echo __('Save Changes', 'wp-represent-map'); ?>">
                </form>
                <h4><?php echo __('Info:', 'wp-represent-map'); ?></h4>
                <?php echo __('Image type: ', 'wp-represent-map'); ?><b>PNG</b><br />
                <?php echo __('Max width: ', 'wp-represent-map'); ?><b>31px</b><br />
                <?php echo __('Max height: ', 'wp-represent-map'); ?><b>42px</b><br />
                <hr>
                
                <h3>
                    <?php echo __('Current markers', 'wp-represent-map'); ?>
                </h3>
                
                <table class="widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <b><?php echo __('Name', 'wp-represent-map'); ?></b>
                            </th>
                            <th>
                                <b><?php echo __('Icon', 'wp-represent-map'); ?></b>
                            </th>
                            <th>
                                <b><?php echo __('Actions', 'wp-represent-map'); ?></b>
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>
                                <b><?php echo __('Name', 'wp-represent-map'); ?></b>
                            </th>
                            <th>
                                <b><?php echo __('Icon', 'wp-represent-map'); ?></b>
                            </th>
                            <th>
                                <b><?php echo __('Actions', 'wp-represent-map'); ?></b>
                            </th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <tr>
                            <td><?php echo __('Default Marker', 'wp-represent-map'); ?></td>
                            <td>
                                <?php $upload_dir = wp_upload_dir(); ?>
                                
                                    <?php if ( file_exists( $upload_dir['basedir'] . '/map-icons/default.png' ) ) : ?>
                                        <img src="<?php echo home_url(); ?>/wp-content/uploads/map-icons/default.png" >
                                    <?php else : ?>
                                        <?php echo __('Not pin yet', 'wp-represent-map'); ?>
                                    <?php endif; ?>
                            </td>
                            <td>---</td>
                        </tr>
                <?php 
                    if ( !empty($categories) ) : ?>
                        <?php foreach( $categories as $category ) : ?>
                            <tr>
                                <td><?php echo $category->name; ?></td>
                                <td>
                                    <?php if ( array_key_exists($category->slug . '.png', $icons) 
                                            && file_exists('../wp-content/uploads/map-icons/' . $icons[$category->slug . '.png'] ) ) : ?>
                                        <img src="<?php echo home_url(); ?>/wp-content/uploads/map-icons/<?php echo $icons[$category->slug . '.png']; ?>" >
                                    <?php else : ?>
                                        <?php echo __('Not pin yet', 'wp-represent-map'); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a 
                                        href="<?php echo admin_url(); ?>/options-general.php?page=wp-represent-map/wp-represent-map.php&tab=markers&delete=<?php echo base64_encode($category->slug); ?>" class="delete">
                                            <?php echo __('Delete', 'wp-represent-map'); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php if ( !empty($category->childs) ) : ?>
                                <?php foreach($category->childs as $child) : ?>
                                    <tr>
                                        <td><?php echo $category->name . ' - ' . $child->name; ?></td>
                                        <td>
                                            <?php if ( array_key_exists($category->slug . '-' . $child->slug . '.png', $icons) 
                                                    && file_exists('../wp-content/uploads/map-icons/' . $icons[$category->slug . '-' . $child->slug . '.png'] ) ) : ?>
                                                <img src="<?php echo home_url(); ?>/wp-content/uploads/map-icons/<?php echo $icons[$category->slug . '-' . $child->slug . '.png']; ?>" >
                                            <?php else : ?>
                                                <?php echo __('Not pin yet', 'wp-represent-map'); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a 
                                                href="<?php echo admin_url(); ?>/options-general.php?page=wp-represent-map/wp-represent-map.php&tab=markers&delete=<?php echo base64_encode($category->slug . '-' . $child->slug); ?>" class="delete">
                                                    <?php echo __('Delete', 'wp-represent-map'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                            <?php endif; ?>
                            
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>
            
            </div>
    </div>

<script>
    jQuery(document).ready(function($) {
        $("#ShowTipLatLng").click(function() {
            $("#TipLatLng").toggle("slow");
            return false;
        });
        
        $("#positioning-click").bind("click", function(){
           $("#markers").hide();
           $("#markers-click").removeClass("nav-tab-active");
           $("#positioning-click").addClass("nav-tab-active");
           $("#positioning").show();
           return false;
        });
        $("#markers-click").bind("click", function(){
           $("#positioning").hide();
           $("#positioning-click").removeClass("nav-tab-active");
           $("#markers-click").addClass("nav-tab-active");
           $("#markers").show();
           return false;
        });
        $(".delete").bind("click", function(){
           return confirm("<?php echo __('Are you sure you want to delete the item icon?', 'wp-represent-map')?>");
        });
        
        
        $(".submit-marker").bind("click", function(){
            return confirm("<?php echo __('This will override the current pin if exists. Do you wish continue?', 'wp-represent-map'); ?>");
        });
        
        <?php if ( isset($_GET['tab']) && 'markers' == $_GET['tab'] ) : ?>
            $("#positioning").hide();
            $("#positioning-click").removeClass("nav-tab-active");
            $("#markers-click").addClass("nav-tab-active");
            $("#markers").show();
        <?php endif; ?>
    });
</script>
    <?php
}
