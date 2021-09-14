<?php

$pt = new \Ardent\Wp\PostType(array(
    'name' => 'speaker',
    'supports' => array('title', 'author'),
    'has_archive' => 'speakers',
    'rewrite' => array('slug' => 'speakers'),
    'exclude_from_search' => true,
    'show_in_menu' => 'edit.php?post_type=sermon',
        ));

$metaboxes = array(
    'General' => array(
        array('type' => 'input.text', 'name' => 'website', 'label' => 'Website'),
        array('type' => 'input.wp.media', 'name' => 'image', 'label' => 'Image'),
        array('type' => 'input.wp.editor', 'name' => 'intro', 'label' => 'Intro'),
        array('type' => 'input.wp.editor', 'name' => 'bio', 'label' => 'Bio')
    )
);

foreach ($metaboxes as $name => $fields) {
    $mb = \Ardent\Wp\Metabox::getInstance('speaker', $name, 'normal', 'high');
    foreach ($fields as $field) {
        $mb->add_field($field);
    }
}

// \Ardent\Salient\Nectar\Header::setFields('speaker');