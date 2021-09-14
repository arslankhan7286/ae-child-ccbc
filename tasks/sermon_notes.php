<?php
add_filter('frm_update_field_options', function($options, $field, $values) {
    if ($field->type === 'ardent_sermon_notes') {
        $options['sermon_notes'] = $values['field_options']["sermon_notes_{$field->id}"];
    }
    return $options;
}, 10, 3);

add_filter('frm_pro_available_fields', function($fields) {
    $fields['ardent_sermon_notes'] = 'Sermon Notes'; // the key for the field and the label
    return $fields;
});

add_filter('frm_before_field_created', function($field_data) {
    if (isset($field_data['type']) && $field_data['type'] == 'ardent_sermon_notes') { //change to your field key
        $field_data['name'] = 'Sermon Notes';
        $defaults = array(
            'sermon_notes' => ''
        );

        foreach ($defaults as $k => $v) {
            $field_data['field_options'][$k] = $v;
        }
    }
    return $field_data;
});

add_action('frm_field_options_form', function($field, $display, $values) {
    global $frm_form, $frm_field;
    if (!isset($field['type']) || $field['type'] != 'ardent_sermon_notes') {
        return;
    }
}, 10, 3);

add_action('frm_form_fields', function($field, $field_name, $c = null) {
    global $frm_form, $frm_field;

    if (!isset($field['type']) || $field['type'] != 'ardent_sermon_notes') {
        return;
    }
    $html_id = 'field_'.@$field['field_key'];
    $op = (int)$_GET['sermon_notes'];
    $recentposts = wp_get_recent_posts(array(
        'post_type' => 'live-stream', 
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_key' => 'start_dt',
        'orderby' => 'meta_value_num',
    ));
    $uid = uniqid("ccbcsn_");
    ?>
        <div id="<?php echo $uid; ?>">
            <?php
                $meta = new \Ardent\Wp\Meta($recentposts[0]['ID']);
                $sermon_notes = preg_replace("/{sermon_note}/i", '<input type="text" class="ccbc_sermon_note" style="height:auto !important;width:auto !important;" />', $meta->sermon_notes);
                echo apply_filters('the_content', $sermon_notes);
            ?>
            <script type="text/javascript" defer="defer">
                jQuery(function($){
                    var sermon_notes = $('#<?php echo $uid; ?> .ccbc_sermon_note');
                    sermon_notes.on('keyup', function(){
                        var data = [];
                        sermon_notes.each(function(){
                            data.push($(this).val());
                        });
                        console.log(JSON.stringify(data));
                        $('#<?php echo esc_attr($html_id) ?>').val(JSON.stringify(data));
                    });
                });
            </script>
            <input id="<?php echo esc_attr($html_id) ?>" name="<?php echo esc_attr($field_name) ?>" type="hidden" value="<?php echo $op; ?>" <?php do_action('frm_field_input_html', $field) ?> />
        </div>
    <?php
}, 10, 2);

add_filter('frmpro_fields_replace_shortcodes', function($value, $tag, $atts, $field){
    if($field->type == 'ardent_sermon_notes'){
        $recentposts = wp_get_recent_posts(array(
            'post_type' => 'live-stream', 
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_key' => 'start_dt',
            'orderby' => 'meta_value_num',
        ));
        $pmeta = new \Ardent\Wp\Meta($recentposts[0]['ID']);
        $notes = json_decode($value);
        $value = $pmeta->sermon_notes;
        foreach($notes as $note){
            $value = preg_replace("/{sermon_note}/i", '<span style="text-decoration:underline;">'.$note.'</span>', $value, 1);
        }
        $value = apply_filters('the_content', $value);
    }
    return $value;
}, 10, 4);