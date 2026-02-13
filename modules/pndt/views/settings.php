<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>PNDT Settings</h4>
                        <hr class="hr-panel-heading">

                        <h5><b>PNDT Consulting Doctors</b></h5>
                        <p class="text-muted">This setting allows selecting the consulting doctors to be displayed on
                            the PNDT form.</p>

                        <div class="form-group">
                            <label>Add Doctor</label>
                            <div class="input-group">
                                <select id="staff_select" class="form-control selectpicker" data-live-search="true">
                                    <option value="">Select Staff Member</option>
                                    <?php foreach ($staff_members as $staff) { ?>
                                        <option value="<?php echo $staff['staffid']; ?>"
                                            data-name="<?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?>"
                                            data-phone="<?php echo $staff['phonenumber']; ?>">
                                            <?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-btn">
                                    <button class="btn btn-info" type="button" onclick="addDoctor()">Add</button>
                                </span>
                            </div>
                        </div>

                        <div id="doctors_list_container" style="margin-top: 20px;">
                            <!-- Doctors will be added here -->
                        </div>

                        <div class="text-right" style="margin-top: 20px;">
                            <button class="btn btn-primary" onclick="saveSettings()">Save Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    var selectedDoctors = [];
    // Load existing settings
    var existingSettings = '<?php echo $pndt_doctors; ?>';

    $(document).ready(function () {
        if (existingSettings) {
            try {
                selectedDoctors = JSON.parse(existingSettings);
                renderDoctors();
            } catch (e) {
                console.error('Error parsing settings', e);
            }
        }
    });

    function addDoctor() {
        var staffId = $('#staff_select').val();
        if (!staffId) return;

        // Check if already added
        var exists = selectedDoctors.find(d => d.id == staffId);
        if (exists) {
            alert_float('warning', 'Doctor already added');
            return;
        }

        var option = $('#staff_select option:selected');
        var name = option.data('name');
        var phone = option.data('phone');

        selectedDoctors.push({
            id: staffId,
            name: name,
            phone: phone,
            license: ''
        });

        renderDoctors();
        $('#staff_select').val('').selectpicker('refresh');
    }

    function removeDoctor(index) {
        selectedDoctors.splice(index, 1);
        renderDoctors();
    }

    function updateDoctor(index, field, value) {
        selectedDoctors[index][field] = value;
    }

    function renderDoctors() {
        var html = '<label>Selected Doctors</label>';

        selectedDoctors.forEach(function (doc, index) {
            html += `
            <div class="panel panel-default" style="margin-bottom: 10px; border: 1px solid #e5e7eb;">
                <div class="panel-body" style="padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="flex-grow: 1;">
                        <h4 style="margin: 0 0 10px 0; font-weight: bold;">DR. ${doc.name.toUpperCase()}</h4>
                         <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <label style="font-size: 11px;">Phone No.</label>
                                    <input type="text" class="form-control input-sm" value="${doc.phone}" onchange="updateDoctor(${index}, 'phone', this.value)" placeholder="Enter Phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <label style="font-size: 11px;">License Number.</label>
                                    <input type="text" class="form-control input-sm" value="${doc.license}" onchange="updateDoctor(${index}, 'license', this.value)" placeholder="Enter License No" style="font-weight: bold;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-left: 20px;">
                        <button class="btn btn-link text-danger" onclick="removeDoctor(${index})" style="font-size: 20px;">&times;</button>
                    </div>
                </div>
            </div>`;
        });

        if (selectedDoctors.length === 0) {
            html += '<div class="alert alert-info">No doctors selected.</div>';
        }

        $('#doctors_list_container').html(html);
    }

    function saveSettings() {
        if (selectedDoctors.length === 0) {
            alert_float('warning', 'Please select at least one doctor');
            // Allow saving empty? Maybe not recommended but possible.
        }

        $.post(admin_url + 'pndt/save_settings', {
            pndt_doctors: JSON.stringify(selectedDoctors),
            // CSRF
            [csrfData['token_name']]: csrfData['hash']
        }, function (response) {
            var res = JSON.parse(response);
            if (res.success) {
                alert_float('success', res.message);
            } else {
                alert_float('danger', 'Failed to save');
            }
        });
    }
</script>