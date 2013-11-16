<?php

/**
 * Template Name: Represent Map
 */

get_header(); 

$map_items = array();
$item_types = get_terms('represent_map_type'); 

?>

<?php query_posts(array('post_type' => 'represent_map')); ?>

<?php if( have_posts()) : while (have_posts()) : the_post(); ?>
        <?php
        $post_ID = get_the_ID();
        $post_meta = get_post_meta($post_ID);

        $terms = get_the_terms($post_ID, 'represent_map_type');

        foreach ($terms as $term) {
            $lat_lng = explode();
            $map_items[$term->slug] = array(
                'id' => $post_ID,
                'address',
                ''
            );
        }
        ?>

    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer();?>