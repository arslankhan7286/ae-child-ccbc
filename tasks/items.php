<?php
add_filter('frm_pro_available_fields', function($fields) {
    $fields['ardent_event_item'] = 'Event Spot Multi'; // the key for the field and the label
    return $fields;
});

add_filter('frm_before_field_created', function($field_data) {
    if (!isset($field_data['type']) || $field_data['type'] != 'ardent_event_item') { //change to your field key
        return $field_data;
    }
    $field_data['name'] = 'Event Spot Multi';
    $defaults = array(
        'eventitem_map' => 0,
    );

    foreach ($defaults as $k => $v) {
        $field_data['field_options'][$k] = $v;
    }

    return $field_data;
});

add_filter('frm_update_field_options', function($options, $field, $values) {
    if ($field->type !== 'ardent_event_item') {
        return $options;
    }
    $options['eventitem_map'] = $values['field_options']["eventitem_map_{$field->id}"];
    return $options;
}, 10, 3);

add_action('frm_field_options_form', function($field, $display, $values) {
    global $frm_form, $frm_field;
    if (!isset($field['type']) || $field['type'] != 'ardent_event_item') {
        return;
    }
    ?>
    <tr>
        <td><label>Event Spots</label></td>
        <td>
            <div class="alignleft" style="margin-right:15px;"><?php echo \Ardent\Html::get(array('type' => 'textarea', 'rows' => '10', 'cols' => '40', 'name' => "field_options[eventitem_map_{$field['id']}]", 'value' => @$field['eventitem_map'], 'id' => "eventitem_map_{$field['id']}")) ?></div>
            <p><strong>In order to add areas to sign up for, use this format and separate options each on a new line:</strong> sometext|anumber</p>
            <table style="padding:0;margin:0;">
                <thead>
                    <tr>
                        <td><strong>Scenario with different jobs to sign up for:</strong></td>
                        <td><strong>Scenario with different times to sign up for:</strong></td>
                        <td><strong>Scenario with different times and jobs to sign up for:</strong></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Greeter|2<br />Usher|4<br />Server|10</td>
                        <td>9:00 AM|5<br />10:00 AM|10<br />11:00 AM|30</td>
                        <td>Greeter 9:00 AM|2<br />Usher 10:00 AM|4<br />Server 11:00 AM|10</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <?php
}, 10, 3);

add_action('frm_form_fields', function($field, $field_name, $c = null) {
    global $frm_form, $frm_field, $wpdb;

    if (!isset($field['type']) || $field['type'] != 'ardent_event_item') {
        return;
    }
    $html_id = @$c['html_id'];
    $options_string = $field['eventitem_map'];

    $options = array();
    foreach ((array) array_filter(explode(PHP_EOL, $options_string)) as $str) {
        list($val, $qty) = explode('|', $str);
        $qty = (int) $qty;
        if (!$qty) {
            $qty = 1;
        }
        $v = $wpdb->_escape($val);
        $sql = "SELECT count(*) AS total FROM `{$wpdb->prefix}frm_item_metas` WHERE `field_id`='{$field['id']}' AND meta_value='{$v}'";
        $sold = array_shift($wpdb->get_results($sql))->total;
        $avail = $qty - $sold;
        if ($avail>0) {
            $options[] = (object) array(
                        'value' => $val,
                        'title' => "{$val} - ({$avail} of {$qty} left)"
            );
        }
    }
    if ($field['value']) { // This is an admin display
        ?><?php echo $field['value'] ?><?php } else {
        ?>
        <?php if (count($options)) { ?>
            <select id="<?php echo esc_attr($html_id) ?>" name="<?php echo esc_attr($field_name) ?>" <?php do_action('frm_field_input_html', $field) ?>>
                <option value="">Select...</option>
                <?php foreach ($options as $option) { ?>
                    <option value="<?php echo $option->value ?>"><?php echo $option->title ?></option>
                <?php } ?>
            </select>
        <?php } else { ?>
            FULL
        <?php } ?>
        <?php
    }
}, 10, 2);


add_filter('frm_validate_field_entry', function($errors, $field, $value) {
    global $wpdb;
    if (@$field->type !== 'ardent_event_item' || !$value) {
        return $errors;
    }
    $options_string = $field->field_options['eventitem_map'];

    $options = array();
    foreach (explode(PHP_EOL, $options_string) as $str) {
        list($val, $qty) = explode('|', $str);
        $qty = (int) $qty;
        if (!$qty) {
            $qty = 1;
        }
        $v = esc_sql($val);
        $sql = "SELECT count(*) AS total FROM `{$wpdb->prefix}frm_item_metas` WHERE `field_id`='{$field->id}' AND meta_value='{$v}'";
        $sold = array_shift($wpdb->get_results($sql))->total;
        $avail = $qty - $sold;
        if ($avail) {
            $options[] = $val;
        }
    }
    if (!in_array($value, $options)) {  // User has selected a bad option
        $errors['field' . $field->id] = 'You have selected an invalid option.';
    }
    return $errors;
}, 20, 3);
