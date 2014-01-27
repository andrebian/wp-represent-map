<?php
$upload_dir = wp_upload_dir();

if ( is_admin() && !is_dir( $upload_dir['basedir'] . '/map-icons' ) ) {
    mkdir( $upload_dir['basedir'] . '/map-icons', 2755 );
}