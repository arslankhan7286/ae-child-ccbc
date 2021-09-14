<?php
add_action( 'save_post', 'clear_sermon_cache', 10, 3 );
add_action( 'publish_post', 'clear_sermon_cache', 10, 3 );
if (isset($_REQUEST['clear_sermons_cache']) ) {
    clear_sermon_cache();
}

function clear_sermon_cache( $post_id = null ) {
    global $wpdb;
    if ($post_id) {
        $post_type = get_post_type($post_id);
        if ( "sermon" != $post_type ) return;
    }
    $files = glob(WP_CONTENT_DIR .'/sermon_filters_cache/*');
    foreach($files as $file){ 
        if(is_file($file)) {
            unlink($file);
        }
    }
}






