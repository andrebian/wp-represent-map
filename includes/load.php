<?php
/**
 * Groups all required files
 * 
 * @since 1.0.0
 */

// Options page
require 'admin/options.php';

// Ajax call
require 'admin/ajax-actions.php';

// Meta boxes
require 'admin/meta-boxes.php';

// Including Shortcodes
require 'theme/shortcodes/load-shortcodes.php';


// Including path configurations
require 'general/path-creation.php';


// including upload
if ( is_admin() ) {
    require '../wp-content/plugins/wp-represent-map/classes/Upload.php';
}
