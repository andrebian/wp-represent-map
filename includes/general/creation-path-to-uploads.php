<?php

if ( is_admin() && !is_dir( '../wp-content/uploads/map-icons' ) ) {
    mkdir('../wp-content/uploads/map-icons');
}