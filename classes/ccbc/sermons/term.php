<?php

namespace Ardent\Ccbc\Sermons;

if (!class_exists('\Ardent\Ccbc\Sermons\Term')) {

    final class Term {
        
        static function getTermImage($term_id, $taxonomy = 'sermon_category'){
            $term = get_term($term_id, $taxonomy);
            $attachment_id = 0;
            while (!is_a($term, 'WP_Error') && !$attachment_id) {
                $attachment_id = (int) get_option("{$taxonomy}_image_{$term->term_id}");
                $term = get_term($term->parent, $taxonomy);
            }
            return $attachment_id;
        }

        static function getTermBGImage($term_id, $taxonomy = 'sermon_category'){
            $term = get_term($term_id, $taxonomy);
            $attachment_id = 0;
            while (!is_a($term, 'WP_Error') && !$attachment_id) {
                $attachment_id = (int) get_option("{$taxonomy}_image_bg_{$term->term_id}");
                $term = get_term($term->parent, $taxonomy);
            }
            return $attachment_id;
        }

        static function fixTaxImages() {
            $categories = get_terms( array(
                'taxonomy' => 'sermon_category',
                'hide_empty' => true,
                'orderby'    => 'count',
                'posts_per_page' => -1,
                'order' => 'DESC'
            ));
            foreach ($categories  as $cat ) { 
                $term_id = $cat->term_id;
                $attach_id = get_term_meta($term_id)['thumbnail_id'];
                // var_dump($term_id, $attach_id[0]);
                $option_name = "sermon_category_image_{$term_id}";
                update_option($option_name, (int) $attach_id[0]);
            }
        }
        
    }
    
}