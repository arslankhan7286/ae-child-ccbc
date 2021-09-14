<?php
add_filter('frm_update_field_options', function($options, $field, $values) {
    if ($field->type === 'ardent_event_ticket') {
        $options['serve_op_limit'] = $values['field_options']["serve_op_limit_{$field->id}"];
        $options['ticket_qty'] = $values['field_options']["ticket_qty_{$field->id}"];
        $options['ticket_qty_per_order'] = $values['field_options']["ticket_qty_per_order_{$field->id}"];
    }
    return $options;
}, 10, 3);

add_filter('frm_pro_available_fields', function($fields) {
    $fields['ardent_event_ticket'] = 'Event Spot Single'; // the key for the field and the label
    return $fields;
});

add_filter('frm_before_field_created', function($field_data) {
    if (isset($field_data['type']) && $field_data['type'] == 'ardent_event_ticket') { //change to your field key
        $field_data['name'] = 'Event Spot Single';
        $defaults = array(
            'serve_op_limit' => 0,
            'ticket_qty' => 0,
            'ticket_qty_per_order' => 1
        );

        foreach ($defaults as $k => $v) {
            $field_data['field_options'][$k] = $v;
        }
    }
    return $field_data;
});

add_action('frm_field_options_form', function($field, $display, $values) {
    global $frm_form, $frm_field;
    if (!isset($field['type']) || $field['type'] != 'ardent_event_ticket') {
        return;
    }
    ?>
    <tr>
        <td><label>Spot Options</label></td>
        <td>
            <style scoped="scoped">label.ardent-html-label{vertical-align: middle;}</style>
            <?php echo \Ardent\Html::get(array('type' => 'select', 'label' => 'Serve Op: ', 'name' => "field_options[serve_op_limit_{$field['id']}]", 'options' => array('' => 'None')+\Ardent\Options::Posts('opportunity'), 'value' => @$field['serve_op_limit'], 'id' => "serve_op_limit_{$field['id']}")) ?><br /><br />
            <?php echo \Ardent\Html::get(array('type' => 'input.text', 'label' => 'Spot Quantity: ', 'name' => "field_options[ticket_qty_{$field['id']}]", 'value' => @$field['ticket_qty'], 'id' => "ticket_qty_{$field['id']}")) ?>
            <?php echo \Ardent\Html::get(array('type' => 'input.text', 'label' => 'Spots Per Order: ', 'name' => "field_options[ticket_qty_per_order_{$field['id']}]", 'value' => @$field['ticket_qty_per_order'], 'id' => "ticket_qty_per_order_{$field['id']}")) ?>
            <p>
                <strong>Serve Op ID:</strong> Used to specify the serve opportunity that you would like to limit to. Leave blank if you want this to be used normally.<br />
                <strong>Spot Quantity:</strong> Used to select the amount of total spots available to sign up for in this field.<br />
                <strong>Spots Per Order:</strong> Used to select the maximum amount of spots a user can sign up for at one time.
            </p>
        </td>
    </tr>
    <?php
}, 10, 3);

add_action('frm_form_fields', function($field, $field_name, $c = null) {
    global $frm_form, $frm_field, $wpdb;
    
    $serve_op_limit = (isset($field['serve_op_limit']) && $field['serve_op_limit'] != '')?(int)$field['serve_op_limit']:false;

    if (!isset($field['type']) || $field['type'] != 'ardent_event_ticket') {
        return;
    }
    
    if($serve_op_limit && $serve_op_limit != (int)$_GET['serve_op']){
        return;
    }
    
    $html_id = @$c['html_id'];
    $total = $field['ticket_qty'];
    $per_order = $field['ticket_qty_per_order'];


    $sql = "SELECT SUM(`meta_value`) AS total FROM `{$wpdb->prefix}frm_item_metas` WHERE `field_id`='{$field['id']}'";
    $sold = (int) @array_shift(@$wpdb->get_results($sql))->total;
    $avail = $total - $sold;
    $min_avail = min($avail, $per_order);

    if ($field['value'] && is_admin() && $_REQUEST['action'] !== 'frm_forms_preview') { // This is an admin display
        ?><?php echo $field['value'] ?><?php } else {
        ?>
        <?php if ($min_avail>0) { ?>
            <select id="<?php echo esc_attr($html_id) ?>" name="<?php echo esc_attr($field_name) ?>" <?php do_action('frm_field_input_html', $field) ?>>
                <?php for ($i = 0; $i <= $min_avail; $i++) { ?>
                    <option<?php echo (int)$field['value']==$i?' selected="selected"':''; ?> value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php } ?>
            </select>
            <span class="availability"><?php echo "{$avail} of {$total} left"; ?></span>
        <?php } else { ?>
            FULL
        <?php } ?>
        <?php
    }
}, 10, 2);

add_filter('frm_validate_field_entry', function($errors, $field, $value) {
    global $wpdb;
    $serve_op_limit = (isset($field->field_options['serve_op_limit']) && $field->field_options['serve_op_limit'] != '')?(int)$field['serve_op_limit']:false;
    if (@$field->type !== 'ardent_event_ticket' || !((int)$value >= 0)) {
        return $errors;
    }
    if($serve_op_limit && (int)$serve_op_limit != (int)$_GET['serve_op']){
        return $errors;
    }
    if(in_array('frm_field_'.$field->id.'_container', explode(',',str_replace(['\\"','[',']'], '', $_REQUEST['frm_hide_fields_'.(int)$_REQUEST['form_id']])))){
        return $errors;
    }
    $total = $field->field_options['ticket_qty'];
    $per_order = $field->field_options['ticket_qty_per_order'];

    $sql = "SELECT SUM(`meta_value`) AS total FROM `{$wpdb->prefix}frm_item_metas` WHERE `field_id`='{$field->id}'";
    $sold = (int) @array_shift(@$wpdb->get_results($sql))->total;
    $avail = $total - $sold;
    $min_avail = min($avail, $per_order);
    if ($value > $min_avail) {
        $errors['field' . $field->id] = "We are sorry, but you have selected too many tickets.";
    }else if(((int)$value === 0 && $field) || ((int)$field->required !== 0 && !$value)){
        $errors['field' . $field->id] = "Please select a number.";
    }
    return $errors;
}, 20, 3);

add_filter('frm_replace_shortcodes', function($html, $field, $data){
    $serve_op_limit = (isset($field['serve_op_limit']) && $field['serve_op_limit'] != '')?(int)$field['serve_op_limit']:false;
    
    if($serve_op_limit && $serve_op_limit != (int)$_GET['serve_op']){
        return '';
    }
    return $html;
}, 50, 3);