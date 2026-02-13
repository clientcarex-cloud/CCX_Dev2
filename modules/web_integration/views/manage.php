<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('web_integration/form'); ?>"
                                class="btn btn-info pull-left display-block"><?php echo _l('new_form'); ?></a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php if (count($forms) > 0) { ?>
                            <table class="table dt-table">
                                <thead>
                                    <th><?php echo _l('id'); ?></th>
                                    <th><?php echo _l('name'); ?></th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th><?php echo _l('created_at'); ?></th>
                                    <th>Created By</th>
                                    <th>Count of Entries</th>
                                    <th><?php echo _l('options'); ?></th>
                                </thead>
                                <tbody>
                                    <?php foreach ($forms as $form) { ?>
                                        <tr>
                                            <td><?php echo $form['id']; ?></td>
                                            <td><a
                                                    href="<?php echo admin_url('web_integration/form/' . $form['id']); ?>"><?php echo $form['name']; ?></a>
                                            </td>
                                            <td><?php echo isset($form['form_category']) ? $form['form_category'] : 'Lead'; ?>
                                            </td>
                                            <td><?php echo isset($form['description']) ? mb_substr(strip_tags($form['description']), 0, 50) . (strlen(strip_tags($form['description'])) > 50 ? '...' : '') : ''; ?>
                                            </td>
                                            <td><?php echo _dt($form['created_at']); ?></td>
                                            <td><?php echo $form['creator_name']; ?></td>
                                            <td><span class="badge"><?php echo $form['entries_count']; ?></span></td>
                                            <td>
                                                <a href="<?php echo admin_url('web_integration/form/' . $form['id']); ?>"
                                                    class="btn btn-default btn-icon"><i class="fa fa-pencil"></i></a>
                                                <a href="<?php echo admin_url('web_integration/entries/' . $form['id']); ?>"
                                                    class="btn btn-default btn-icon" data-toggle="tooltip" title="Entries"><i
                                                        class="fa fa-list"></i></a>
                                                <a href="<?php echo site_url('web_integration/forms/index/' . $form['form_key']); ?>"
                                                    class="btn btn-default btn-icon" target="_blank" data-toggle="tooltip"
                                                    title="View Link"><i class="fa fa-link"></i></a>
                                                <a href="#" class="btn btn-default btn-icon"
                                                    onclick="get_iframe_code(<?php echo $form['id']; ?>); return false;"
                                                    data-toggle="tooltip" title="Get Iframe Code"><i class="fa fa-code"></i></a>
                                                <a href="#" class="btn btn-default btn-icon"
                                                    onclick="get_api_details(<?php echo $form['id']; ?>); return false;"
                                                    data-toggle="tooltip" title="API Integration"><i class="fa fa-plug"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('no_forms_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="iframe_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Iframe Code</h4>
            </div>
            <div class="modal-body">
                <textarea id="iframe_code_area" class="form-control" rows="5"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="api_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">API Integration</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#api_details" aria-controls="api_details" role="tab"
                            data-toggle="tab">API Details</a></li>
                    <li role="presentation"><a href="#api_example" aria-controls="api_example" role="tab"
                            data-toggle="tab">Code Example</a></li>
                    <li role="presentation"><a href="#api_test" aria-controls="api_test" role="tab"
                            data-toggle="tab">Test API</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="api_details">
                        <p><strong>Endpoint URL:</strong> <span id="api_url"></span></p>
                        <p id="api_secret_key_wrapper" class="hide"><strong>Secret Key:</strong> <span
                                id="api_secret_key"></span> <span class="text-danger">(Required)</span></p>
                        <p><strong>Method:</strong> POST</p>
                        <hr />
                        <h5>Parameters:</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Parameter Name</th>
                                    <th>Required?</th>
                                </tr>
                            </thead>
                            <tbody id="api_params"></tbody>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="api_example">
                        <textarea id="curl_example" class="form-control" rows="15" readonly></textarea>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="api_test">
                        <form id="api_test_form">
                            <input type="hidden" id="test_form_key" name="form_key">
                            <div id="test_form_fields"></div>
                            <button type="submit" class="btn btn-primary">Send Test Request</button>
                        </form>
                        <div id="api_test_result" class="hide mtop15">
                            <h5>Response:</h5>
                            <pre id="api_response_content"></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    function get_iframe_code(id) {
        $.get(admin_url + 'web_integration/get_iframe_code/' + id, function (response) {
            response = JSON.parse(response);
            $('#iframe_code_area').val(response.iframe_code);
            $('#iframe_modal').modal('show');
        });
    }

    function get_api_details(id) {
        $('#api_test_result').addClass('hide');
        $.get(admin_url + 'web_integration/get_api_details/' + id, function (response) {
            response = JSON.parse(response);
            var form = response.form;
            var fields = response.fields;
            var url = '<?php echo site_url("web_integration/forms/submit/"); ?>' + form.form_key;

            $('#api_url').text(url);
            $('#test_form_key').val(form.form_key);

            if (form.secret_key) {
                $('#api_secret_key_wrapper').removeClass('hide');
                $('#api_secret_key').text(form.secret_key);
            } else {
                $('#api_secret_key_wrapper').addClass('hide');
            }

            var paramsHtml = '';
            var testFieldsHtml = '';
            var curlData = '';

            if (form.secret_key) {
                paramsHtml += '<tr><td>secret_key</td><td>Yes</td></tr>';
                curlData += ' -F "secret_key=' + form.secret_key + '"';
                // Add hidden input for test form
                testFieldsHtml += '<input type="hidden" name="secret_key" value="' + form.secret_key + '">';
            }

            $.each(fields, function (index, field) {
                if (field.is_visible == 1) {
                    paramsHtml += '<tr><td>' + field.field_id + '</td><td>' + (field.is_required == 1 ? 'Yes' : 'No') + '</td></tr>';

                    testFieldsHtml += '<div class="form-group"><label>' + field.custom_label + '</label>';
                    testFieldsHtml += '<input type="text" name="' + field.field_id + '" class="form-control" ' + (field.is_required == 1 ? 'required' : '') + '></div>';

                    curlData += ' -F "' + field.field_id + '=value"';
                }
            });

            if (response.appointment_info && response.appointment_info.is_active) {
                // Add Appointment specific params
                paramsHtml += '<tr><td>start_time</td><td>Yes (Format: HH:MM:SS)</td></tr>';
                paramsHtml += '<tr><td>end_time</td><td>No (Auto-calculated if omitted)</td></tr>';

                curlData += ' \\\n -F "start_time=09:00:00"';

                testFieldsHtml += '<div class="form-group"><label>Start Time</label><input type="text" name="start_time" class="form-control" placeholder="09:00:00"></div>';

                // Show info about fetching slots
                var slotsInfo = '<div class="alert alert-info mtop15"><strong>Appointment Linking Enabled</strong><br/>';
                slotsInfo += 'To fetch available slots, make a POST request to: <br/><code>' + response.appointment_info.slots_url + '</code><br/>';
                slotsInfo += 'Parameters: <code>doctor_id</code>, <code>date</code> (Y-m-d)</div>';

                $('#api_details').append(slotsInfo);
            }

            $('#api_params').html(paramsHtml);
            $('#test_form_fields').html(testFieldsHtml);

            var curlExample = 'curl -X POST ' + url + ' \\\n' + curlData;
            $('#curl_example').val(curlExample);

            $('#api_modal').modal('show');

            // Handle Test Form Submit
            $('#api_test_form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (data) {
                        $('#api_test_result').removeClass('hide');
                        $('#api_response_content').text(JSON.stringify(data, null, 4));
                    },
                    error: function (xhr) {
                        $('#api_test_result').removeClass('hide');
                        $('#api_response_content').text('Error: ' + xhr.status + ' ' + xhr.statusText + '\n' + xhr.responseText);
                    }
                });
            });
        });
    }
</script>