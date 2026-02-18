<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$fields_settings = get_option('ccx_leads_field_settings');
$ordering_settings = get_option('ccx_leads_ordering_settings');
$fields_map = [];
if ($fields_settings) {
    $decoded = json_decode($fields_settings, true);
    foreach ($decoded as $f) {
        $fields_map[$f['slug']] = $f;
    }
}

// Fallback field config getter
$get_field = function($slug) use ($fields_map) {
    return isset($fields_map[$slug]) ? $fields_map[$slug] : ['status' => 1, 'mandatory' => 0];
};

// Default ordering if not set
if (empty($ordering_settings)) {
    // Construct default matching the hardcoded layout conceptually
    $ordering_settings = json_encode([
        ['slug' => 'name', 'width' => 12],
        ['slug' => 'phonenumber', 'width' => 12],
        ['slug' => 'email', 'width' => 12],
        ['slug' => 'title', 'width' => 12],
        ['slug' => 'website', 'width' => 12],
        ['slug' => 'description', 'width' => 12],
        ['slug' => 'address', 'width' => 6],
        ['slug' => 'city', 'width' => 6],
        ['slug' => 'state', 'width' => 4],
        ['slug' => 'country', 'width' => 4],
        ['slug' => 'zip', 'width' => 4],
        ['slug' => 'status', 'width' => 6],
        ['slug' => 'source', 'width' => 6],
        ['slug' => 'assigned', 'width' => 6],
        ['slug' => 'lead_value', 'width' => 6],
        ['slug' => 'priority', 'width' => 12],
    ]);
}
$ordering = json_decode($ordering_settings, true);

// Fetch Custom Fields for looking up options/types
$custom_fields_map = [];
if($this->db->table_exists(db_prefix() . 'ccx_leads_custom_fields')){
    $c_fields = $this->db->where('status', 1)->get(db_prefix() . 'ccx_leads_custom_fields')->result_array();
    foreach($c_fields as $cf) {
        $custom_fields_map['custom_' . $cf['id']] = $cf;
    }
}
?>

<div class="row">
    <?php
    foreach ($ordering as $field_config) {
        $slug = $field_config['slug'];
        $width = isset($field_config['width']) ? $field_config['width'] : 12;
        
        // Check if custom field
        $is_custom = strpos($slug, 'custom_') === 0;
        
        if ($is_custom) {
            $f = isset($custom_fields_map[$slug]) ? $custom_fields_map[$slug] : null;
            if (!$f) continue; // Custom field not found or distinct
        } else {
            $f = $get_field($slug);
            if (!isset($f['status']) || $f['status'] != 1) continue;
        }
        
        $mandatory = isset($f['mandatory']) && $f['mandatory'] == 1;
        $req_attr = $mandatory ? 'required' : '';
        $value = '';
        
        // Resolve value from $lead object
        if ($is_custom) {
             if(isset($lead)) {
                $field_id = str_replace('custom_', '', $slug);
                $val_row = $this->db->where('lead_id', $lead->id)->where('field_id', $field_id)->get(db_prefix() . 'ccx_leads_custom_values')->row();
                $value = $val_row ? $val_row->value : '';
            }
        } else {
            $value = isset($lead) && isset($lead->$slug) ? $lead->$slug : '';
        }
        
        // Wrapper with width
        echo '<div class="col-md-' . $width . '">';
        
        if ($is_custom) {
            $input_name = 'custom_fields[' . $f['id'] . ']';
            $label = $f['name'];
            $attrs = [];
            if ($mandatory) $attrs['required'] = true;
            
             if($f['type'] == 'text' || $f['type'] == 'number' || $f['type'] == 'email') {
                echo render_input($input_name, $label, $value, $f['type'], $attrs);
            } elseif($f['type'] == 'date') {
                echo render_date_input($input_name, $label, $value, $attrs);
            } elseif($f['type'] == 'textarea') {
                echo render_textarea($input_name, $label, $value, $attrs);
            } elseif($f['type'] == 'select') {
                $options = preg_split('/[\r\n,]+/', $f['options'], -1, PREG_SPLIT_NO_EMPTY);
                $select_options = [];
                foreach($options as $opt) {
                    $opt = trim($opt);
                    if($opt !== '') $select_options[] = ['id' => $opt, 'name' => $opt];
                }
                echo render_select($input_name, $select_options, ['id', 'name'], $label, $value, $attrs);
            } elseif($f['type'] == 'multiselect') {
                $options = preg_split('/[\r\n,]+/', $f['options'], -1, PREG_SPLIT_NO_EMPTY);
                $select_options = [];
                foreach($options as $opt) {
                     $opt = trim($opt);
                    if($opt !== '') $select_options[] = ['id' => $opt, 'name' => $opt];
                }
                $attrs['multiple'] = true;
                $selected_values = $value;
                if (is_string($value) && strpos($value, ',') !== false) {
                    $selected_values = explode(',', $value);
                     $selected_values = array_map('trim', $selected_values);
                } elseif (is_string($value) && !empty($value)) {
                    $selected_values = [$value];
                }
                echo render_select($input_name . '[]', $select_options, ['id', 'name'], $label, $selected_values, $attrs);
            }
        } else {
            // Standard Fields
            switch ($slug) {
                case 'name':
                    ?>
                    <div class="form-group">
                        <label for="name" class="control-label">
                            <?php echo _l('ccx_leads_name'); ?>
                            <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                    </div>
                    <?php
                    break;
                case 'phonenumber':
                    ?>
                    <div class="form-group" id="phone_group">
                        <label for="phonenumber" class="control-label">
                            <?php echo _l('ccx_leads_phonenumber'); ?>
                            <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="text" id="phonenumber" name="phonenumber" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                        <span id="phone_counter" class="text-muted small pull-right" style="display:none;"></span>
                        <span id="phone_duplicate_error" class="text-danger small" style="display:none;"></span>
                    </div>
                    <?php
                    break;
                case 'email':
                     ?>
                    <div class="form-group">
                        <label for="email" class="control-label">
                            <?php echo _l('ccx_leads_email'); ?>
                            <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                    </div>
                    <?php
                    break;
                case 'title':
                      ?>
                    <div class="form-group">
                        <label for="title" class="control-label">
                            <?php echo _l('ccx_leads_title'); ?>
                             <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="text" id="title" name="title" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                    </div>
                    <?php
                    break;
                case 'website':
                     ?>
                    <div class="form-group">
                        <label for="website" class="control-label">
                            <?php echo _l('ccx_leads_website'); ?>
                             <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="text" id="website" name="website" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                    </div>
                    <?php
                    break;
                 case 'description':
                     ?>
                    <div class="form-group">
                        <label for="description" class="control-label">
                            <?php echo _l('ccx_leads_description'); ?>
                            <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="4" <?php echo $req_attr; ?>><?php echo $value; ?></textarea>
                    </div>
                    <?php
                    break;
                case 'address':
                     ?>
                    <div class="form-group">
                        <label for="address" class="control-label">
                            <?php echo _l('ccx_leads_address'); ?>
                             <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="text" id="address" name="address" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                    </div>
                    <?php
                    break;
                 case 'city':
                     ?>
                    <div class="form-group">
                        <label for="city" class="control-label">
                            <?php echo _l('ccx_leads_city'); ?>
                             <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="text" id="city" name="city" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                    </div>
                    <?php
                    break;
                case 'state':
                     ?>
                    <div class="form-group">
                        <label for="state" class="control-label">
                            <?php echo _l('ccx_leads_state'); ?>
                             <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="text" id="state" name="state" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                    </div>
                    <?php
                    break;
                case 'zip':
                     ?>
                    <div class="form-group">
                        <label for="zip" class="control-label">
                            <?php echo _l('ccx_leads_zip'); ?>
                             <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <input type="text" id="zip" name="zip" class="form-control" value="<?php echo $value; ?>" <?php echo $req_attr; ?>>
                    </div>
                    <?php
                    break;
                case 'country':
                     ?>
                    <div class="form-group">
                        <label for="country" class="control-label">
                            <?php echo _l('ccx_leads_country'); ?>
                             <?php if ($mandatory) echo '<span class="text-danger">*</span>'; ?>
                        </label>
                        <select id="country" name="country" class="form-control" <?php echo $req_attr; ?> data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <option value=""></option>
                            <?php
                            $selected = (isset($lead) ? $lead->country : get_option('customer_default_country'));
                            foreach (get_all_countries() as $country) {
                                ?>
                                <option value="<?php echo $country['country_id']; ?>" <?php if ($selected == $country['country_id']) { echo 'selected'; } ?>>
                                    <?php echo $country['short_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <?php
                    break;
                case 'status':
                    $attrs = [];
                    if ($mandatory) $attrs['required'] = true;
                    echo render_select('status', $statuses ?? [], ['id', 'name'], 'status', (isset($lead) ? $lead->status : ''), $attrs);
                    break;
                case 'source':
                    echo render_select('source', $sources ?? [], ['id', 'name'], 'ccx_leads_source', (isset($lead) ? $lead->source : ''));
                    break;
                case 'assigned':
                    $attrs = [];
                    if ($mandatory) $attrs['required'] = true;
                    $selected_assigned = (isset($lead) ? $lead->assigned : get_staff_user_id());
                    echo render_select('assigned', $staff_members ?? [], ['staffid', ['firstname', 'lastname']], 'assigned', $selected_assigned, $attrs);
                    break;
                case 'lead_value':
                    $attrs = ['step' => '0.01'];
                    if ($mandatory) $attrs['required'] = true;
                    echo render_input('lead_value', 'lead_value', (isset($lead) ? $lead->lead_value : ''), 'number', $attrs);
                    break;
                case 'priority':
                    $attrs = [];
                    if ($mandatory) $attrs['required'] = true;
                    $priorities_data = isset($priorities) ? $priorities : [
                        ['id' => 0, 'name' => _l('not_set')],
                        ['id' => 1, 'name' => _l('low')],
                        ['id' => 2, 'name' => _l('medium')],
                        ['id' => 3, 'name' => _l('high')],
                    ];
                    // Handle structure difference if priorities come from DB vs hardcoded
                    // DB: priorityid, name. Hardcoded: id, name.
                    // Let's normalize key or just check
                    $key = isset($priorities_data[0]['priorityid']) ? 'priorityid' : 'id';
                    
                    echo render_select('priority', $priorities_data, [$key, 'name'], 'priority', (isset($lead) ? $lead->priority : 0), $attrs);
                    break;
            }
        }
        echo '</div>'; // End col
    }
    ?>
</div>
