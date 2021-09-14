<?php

$speakers = array(
    0 => 'Select...'
);
if(is_admin()){
    foreach (get_posts(array(
        'post_type' => 'Speaker',
        'numberposts' => -1,
        'orderby' => 'post_title',
        'order' => 'DESC'
    )) as $speaker) {
        $speakers[] = (object) array(
                    'value' => $speaker->ID,
                    'title' => $speaker->post_title
        );
    }
}

$posttype = new \Ardent\Wp\PostType(array(
    'name' => 'Sermon',
    'supports' => array('title', 'comments'),
    'has_archive' => 'sermons',
    'rewrite' => array('slug' => 'sermon'),
    'taxonomies' => array('sermon_category'),
    'menu_icon' => 'dashicons-book-alt'
));

register_taxonomy('sermon_category', 'sermon', array(
    'hierarchical' => true,
    'show_admin_column' => true,
    'rewrite' => array(
        'slug' => 'sermons',
        'hierarchical' => true
    )
));

$metaboxes = array(
    'General' => array(
        array('type' => 'textarea', 'name' => 'description', 'label' => 'Description'),
        array('type' => 'select', 'name' => 'speaker', 'label' => 'Speaker', 'options' => $speakers),
        array('type' => 'input.text', 'name' => 'mp3', 'label' => 'MP3 URL', 'style' => 'width:60%;'),
        array('type' => 'input.text', 'name' => 'references', 'label' => 'Scripture References', 'style' => 'width:60%;'),
        array('type' => 'input.text', 'name' => 'video_url', 'label' => 'Video URL', 'style' => 'width:60%;'),
        array('type' => 'input.text', 'name' => 'video', 'label' => 'Truthcasting ID'),
        array('type' => 'input.text', 'name' => 'vimeo', 'label' => 'Vimeo ID'),
        array('type' => 'input.text', 'name' => 'youtube', 'label' => 'Youtube Embed ID'),
        array('type' => 'input.xlist', 'name' => 'pdfs', 'label' => 'PDF Files', 'form' => array(
                array('type' => 'input.text', 'name' => 'title', 'label' => 'Title'),
                array('type' => 'input.text', 'name' => 'url', 'label' => 'URL'),
            ))
    )
);

foreach ($metaboxes as $name => $fields) {
    $mb = \Ardent\Wp\Metabox::getInstance('sermon', $name, 'normal', 'high');
    foreach ($fields as $field) {
        $mb->add_field($field);
    }
}

$add_fields = function($term) {
    echo \Ardent\Html::get(array('type' => 'input.wp.media', 'name' => 'category_image', 'label' => 'Image'));
    echo \Ardent\Html::get(array('type' => 'input.wp.media', 'name' => 'category_image_bg', 'label' => 'Background Image'));
    return;
};
$edit_fields = function($term) {
    $option_name = 'sermon_category_image_' . $term->term_id;
    $image_id = (int) get_option($option_name);
    echo \Ardent\Html::get(array('type' => 'input.wp.media', 'value' => $image_id, 'name' => 'category_image', 'label' => 'Image'));
    $option_name2 = 'sermon_category_image_bg_' . $term->term_id;
    $image_id2 = (int) get_option($option_name2);
    echo \Ardent\Html::get(array('type' => 'input.wp.media', 'value' => $image_id2, 'name' => 'category_image_bg', 'label' => 'Background Image'));
    return;
};
$save_func = function($term_id) {
    if (isset($_REQUEST['category_image'])) {
    $option_name = "sermon_category_image_{$term_id}";
        update_option($option_name, (int) @$_REQUEST['category_image']);
    }
    if (isset($_REQUEST['category_image_bg'])) {
        $option_name = "sermon_category_image_bg_{$term_id}";
        update_option($option_name, (int) @$_REQUEST['category_image_bg']);
    }
    return;
};

add_action('sermon_category_add_form_fields', $add_fields);
add_action('sermon_category_edit_form_fields', $edit_fields);

add_action('edited_sermon_category', $save_func);
add_action('create_sermon_category', $save_func);

// \Ardent\Salient\Nectar\Header::setFields('sermon');

$series = array(
    0 => 'Select...'
);
$categories = get_terms( array(
    'taxonomy' => 'sermon_category',
    'hide_empty' => true,
    'orderby'    => 'count',
    'posts_per_page' => -1,
    'order' => 'DESC'
));
foreach ($categories  as $cat ) { 
    $series[] = (object) array(
    'value' => $cat->term_id,
    'title' => $cat->name
    );
}

$settings = array(
    'Sermon List/Item' => array(
        'Content' => array(
            array('type' => 'input.text', 'name' => 'top_title', 'label' => 'Title'),
            array('type' => 'input.wp.media', 'name' => 'header_image', 'label' => 'Header Image'),
            array('type' => 'input.wp.media', 'name' => 'default_image', 'label' => 'Default Sermon Image'),
            array('type' => 'input.wp.editor', 'name' => 'top_content', 'label' => 'Content'),
            array('type' => 'select', 'name' => 'featured_series', 'label' => 'Featured Series', 'options' => $series),
            array('type' => 'button', 'name' => 'clear_sermons_cache', 'text' => 'Clear Sermons Cache', 'href' => get_site_url() . '?clear_sermons_cache=true'),
        )
    )
);

$config = \Ardent\Ccbc\Sermons::getConfig();
$config->set_settings($settings);
