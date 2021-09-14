<?php

$pt = new \Ardent\Wp\PostType(array(
    'name' => 'Staff',
    'supports' => array('title', 'editor','thumbnail'),
    'has_archive' => 'staff',
    'rewrite' => array('slug' => 'staff'),
    'taxonomies' => array('staff_category'),
    'label' => 'Staff',
    'labels' => array('name' => 'Staff'),
    'menu_icon' => 'dashicons-groups'
));

register_taxonomy('staff_category', 'staff', array(
    'hierarchical' => true,
    'show_admin_column' => true,
    'rewrite' => array('slug' => 'staff-ministry', 'with_front' => true)
));

$metaboxes = array(
    'General' => array(
        array('type' => 'input.text', 'name' => 'first_name', 'label' => 'First Name'),
        array('type' => 'input.text', 'name' => 'last_name', 'label' => 'Last Name'),
        array('type' => 'input.text', 'name' => 'title', 'label' => 'Title'),
        array('type' => 'input.text', 'name' => 'email', 'label' => 'Email'),
        array('type' => 'input.text', 'name' => 'phone', 'label' => 'Phone'),
        array('type' => 'input.wp.editor', 'name' => 'bio', 'label' => 'Short Bio'),
        // array('type' => 'input.wp.media', 'name' => 'staff_headshot', 'label' => 'Staff Headshot'),
        array('type' => 'input.text', 'name' => 'link', 'label' => 'Link'),
        array('type' => 'input.text', 'name' => 'link_title', 'label' => 'Link Title'),
    )
);

foreach ($metaboxes as $name => $fields) {
    $mb = \Ardent\Wp\Metabox::getInstance('staff', $name, 'normal', 'high');
    foreach ($fields as $field) {
        $mb->add_field($field);
    }
}

$sidemb = \Ardent\Wp\Metabox::getInstance('staff', 'Ordering', 'side');
$sidemb->add_field(array('type' => 'input.text', 'name' => '_custom_order', 'label' => 'Custom Order', 'style' => "width:100%;text-indent:0px;line-height:normal;"));

$add_fields = function($term) {
    $option_name = 'staff_category_exclude_' . $term->term_id;
    $exclude_id = (int) get_option($option_name);
    ?><div class="form-field"><label for="category_exclude">Exclude from staff menu</label><?php
    echo \Ardent\Html::get(array('type' => 'select', 'style' => 'min-width:134px;', 'name' => 'category_exclude', 'value' => $exclude_id, 'options' => array(0 => 'No', 1 => 'Yes')));
    ?></div><?php
    return;
};
$edit_fields = function($term) {
    $option_name = 'staff_category_exclude_' . $term->term_id;
    $exclude_id = (int) get_option($option_name);
    ?><tr class="form-field"><th scope="row"><label for="category_exclude">Exclude from staff menu</label></th><td><?php
    echo \Ardent\Html::get(array('type' => 'select', 'style' => 'min-width:134px;', 'name' => 'category_exclude', 'value' => $exclude_id, 'options' => array(0 => 'No', 1 => 'Yes')));
    ?></td></tr><?php
    return;
};
$save_func = function($term_id) {
    $option_name = "staff_category_exclude_{$term_id}";
    return update_option($option_name, (int) @$_REQUEST['category_exclude']);
};

add_action('staff_category_add_form_fields', $add_fields);
add_action('staff_category_edit_form_fields', $edit_fields);

add_action('edited_staff_category', $save_func);
add_action('create_staff_category', $save_func);

// \Ardent\Salient\Nectar\Header::setFields('staff');

$categories = get_categories(array(
    'taxonomy' => 'staff_category',
    'hide_empty' => false,
    'hierarchical' => true,
    'orderby' => 'name',
    'order' => 'ASC'
));
$cats = array(
    '0' => 'All'
);
foreach ($categories as $cat) {
    $exclude = get_option('staff_category_exclude_'.$cat->term_id);
    if(!$exclude){
        $cats[] = (object) array(
            'value' => $cat->term_id,
            'title' => $cat->name
        );
    }
}
// $settings = array(
//     'Staff List/Item' => array(
//         'Content' => array(
//             array('type' => 'input.text', 'name' => 'top_title', 'label' => 'Title'),
//             array('type' => 'input.wp.media', 'name' => 'header_image', 'label' => 'Header Image'),
//             array('type' => 'input.wp.media', 'name' => 'header_image_item', 'label' => 'Item Header Image'),
//             array('type' => 'select', 'name' => 'initial_category', 'label' => 'Initial Category', 'options' => $cats),
//             array('type' => 'input.wp.editor', 'name' => 'top_content', 'label' => 'Content'),
//         )
//     )
// );

// $config = \Ardent\Ccbc\Staff::getConfig();
// $config->set_settings($settings);