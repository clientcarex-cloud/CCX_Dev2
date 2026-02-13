
$(document).ready(function () {
    $('.toggle-mask').on('click', function (e) {
        e.preventDefault();
        var $span = $(this).prev('.masked-content');
        var real = $span.data('real');
        var current = $span.text();

        if (!$span.data('masked')) {
            $span.data('masked', current);
        }

        if (current.indexOf('*') !== -1) {
            // Show real
            $span.text(real);
            $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            // Show masked
            $span.text($span.data('masked'));
            $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#form_add_file').on('submit', function (e) {
        e.preventDefault();

        if (!current_patient_id) {
            alert('No patient selected');
            return;
        }

        var input = document.getElementById('file_input');
        if (!input.files || input.files.length === 0) {
            alert_float('warning', 'Please select a file first');
            return;
        }

        var formData = new FormData(this);
        formData.append('patient_id', current_patient_id);
        // Append CSRF if available (Standard Perfex usually needs it in FormData)
        if (typeof csrfData !== 'undefined') {
            formData.append(csrfData['token_name'], csrfData['hash']);
        }

        var btn = $(this).find('button[type="submit"]');
        var originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');

        $.ajax({
            url: admin_url + 'patient_master_modal/patient_master_modal/add_file',
            type: 'POST',
            data: formData,
            success: function (response) {
                var result = JSON.parse(response);
                if (result.success) {
                    $('#file_input').val(''); // Reset input
                    updateFileLabel(document.getElementById('file_input')); // Reset UI
                    $('.table-patient-files').DataTable().ajax.reload();
                    alert_float('success', result.message);
                } else {
                    alert_float('danger', result.message);
                }
            },
            error: function () {
                alert_float('danger', 'Error uploading file');
            },
            complete: function () {
                btn.prop('disabled', false).html(originalText);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    // Lazy load handling for all tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Add other cases if new tabs need lazy loading
    });
});

function view_prescription(url) {
    if (url.indexOf('?') > -1) {
        url += '&popup=1';
    } else {
        url += '?popup=1';
    }
    $('#prescription_iframe').attr('src', url);
    $('#prescription_modal').modal('show');
}

// Clear src on close to stop playing media or clear memory
$('#prescription_modal').on('hidden.bs.modal', function () {
    $('#prescription_iframe').attr('src', '');
});

// Store current patient ID for the modal
var current_patient_id = null;
var current_prescription_id = null;
// Track loaded tabs for the current patient
var loaded_tabs = {};

function open_action_modal(btn, id) { // id here is prescription_id, but we need patient_id
    var button = $(btn);
    current_prescription_id = id;



    // Use .attr to ensure we get the string value exactly as in DOM, preventing jQuery data type inferencing issues
    var new_patient_id = button.attr('data-patient-id');

    // Reset loaded tabs if patient changed
    if (current_patient_id !== new_patient_id) {
        loaded_tabs = {};
        current_patient_id = new_patient_id;
    }

    var title = button.data('title') || '';
    var name = button.data('name');
    var gender = button.data('gender') || '';
    var mr = button.data('mr');
    var next = button.data('next');
    var days = button.data('days');
    var invert = button.data('invert'); // 1 if past (overdue)

    // Capitalize Gender
    if (gender) {
        gender = gender.charAt(0).toUpperCase() + gender.slice(1);
    }

    // Calculate Relative String
    var relativeString = '';
    if (days !== undefined && days !== null) {
        if (days == 0) {
            relativeString = 'Today only!';
        } else if (invert) {
            relativeString = days + ' Day' + (days > 1 ? 's' : '') + ' ago';
        } else {
            // Future
            relativeString = days + ' days left';
        }
    }


    // Construct Title String
    var titleHtml = '';
    if (title) titleHtml += title + ' ';
    titleHtml += name;

    if (gender) titleHtml += ' | ' + gender;

    if (mr) titleHtml += ' | <span class="label label-info" style="font-size: 85%;">' + mr + '</span>';

    if (next) titleHtml += ' | ' + next + ' & ' + relativeString;

    $('#action_modal_title').html(titleHtml);

    // Reset Tabs
    $('.custom-action-tabs a[href="#tab_overview"]').tab('show');
    // Mark overview as loaded since we load it immediately below
    loaded_tabs['#tab_overview'] = current_patient_id;

    // Clear other tabs content
    $('#tab_tickets').empty();

    // Reset Inputs (Notes, Reminders, Tasks)
    $('#note_description').val('');
    $('#reminder_description').val('');
    if (typeof moment === 'function') {
        $('#reminder_date').val(moment().format('YYYY-MM-DD HH:mm'));
    } else {
        // Fallback if moment is missing (rare in Perfex but possible)
        var now = new Date();
        var yyyy = now.getFullYear();
        var mm = String(now.getMonth() + 1).padStart(2, '0');
        var dd = String(now.getDate()).padStart(2, '0');
        var hh = String(now.getHours()).padStart(2, '0');
        var min = String(now.getMinutes()).padStart(2, '0');
        $('#reminder_date').val(yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + min);
    }
    $('#reminder_notify_by_email').prop('checked', false);
    $('#task_name').val('');
    $('#task_duedate').val('');
    $('#task_priority').selectpicker('val', '2');

    // Remove Eager Loading - ONLY load Overview
    // CHECK CLIENT SIDE DATA FIRST
    var hasClientData = button.data('age') !== undefined;

    if (hasClientData) {
        // Collect Data
        var clientData = {
            name: (title ? title + ' ' : '') + name,
            gender: gender,
            age: button.data('age'),
            age_unit: button.data('age-unit'),
            mr: mr,
            uid: button.data('uid'),
            phone: button.data('phone'),
            email: button.data('email'),
            address: button.data('address'),
            referral_doc: button.data('referral-doc'),
            attender: button.data('attender'),
            reg_date: button.data('reg-date'),
            visits: button.data('visits'),
            groups: button.data('groups')
        };

        // Store Ticket Data Global
        current_patient_ticket_data = {
            patient_id: current_patient_id,
            total_contacts: button.data('total-contacts'),
            primary_contact_id: button.data('primary-contact-id')
        };

        var html = buildOverviewHtml(clientData);
        $('#tab_overview').html(html);

    } else {
        // Fallback
        current_patient_ticket_data = null; // Clear if not available

        // Fallback to AJAX
        $('#tab_overview').html('<div class="text-center mtop30"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        $.get(admin_url + 'patient_master_modal/patient_master_modal/get_patient_overview/' + current_patient_id, function (response) {
            $('#tab_overview').html(response);
        });
    }

    $('#action_modal').modal('show');
}

function buildOverviewHtml(data) {
    // Masking Helper
    var maskedPhone = '';
    var realPhone = data.phone ? String(data.phone) : '';
    if (realPhone.length > 4) {
        maskedPhone = realPhone.substring(0, 2) + '******' + realPhone.substring(realPhone.length - 2);
    } else {
        maskedPhone = realPhone;
    }

    var groupBadges = '';
    if (data.groups) {
        var groups = data.groups.toString().split(',');
        groups.forEach(function (g) {
            if (g.trim()) {
                groupBadges += '<span class="tag-badge tag-purple" style="margin-right: 5px; margin-bottom: 5px;">' + g.trim() + '</span>';
            }
        });
    }

    // HTML Construction - Matching overview.php styles
    var html = '<div class="row">';

    // Patient Details
    html += '<div class="col-md-12"><div class="overview-section"><div class="overview-header"><i class="fa fa-user-circle-o"></i><h3>Patient Details</h3></div><div class="info-grid">';
    html += '<div class="info-item"><span class="info-label">Full Name</span><span class="info-value">' + (data.name || '<span class="empty">N/A</span>') + '</span></div>';
    html += '<div class="info-item"><span class="info-label">Gender</span><span class="info-value">' + (data.gender || '<span class="empty">N/A</span>') + '</span></div>';
    html += '<div class="info-item"><span class="info-label">Age</span><span class="info-value">' + (data.age ? data.age + ' ' + (data.age_unit || '') : '<span class="empty">N/A</span>') + '</span></div>';
    html += '<div class="info-item"><span class="info-label">MR Number</span><span class="info-value"><span class="tag-badge tag-blue">' + (data.mr || 'N/A') + '</span></span></div>';
    html += '<div class="info-item"><span class="info-label">UID / Aadhaar</span><span class="info-value">' + (data.uid || '<span class="empty">Not Provided</span>') + '</span></div>';
    html += '</div></div></div>';

    // Contact Details
    html += '<div class="col-md-6"><div class="overview-section" style="min-height: 220px;"><div class="overview-header"><i class="fa fa-address-card-o" style="color: #059669; background: #ecfdf5;"></i><h3>Contact Information</h3></div><div class="info-grid" style="grid-template-columns: 1fr;">';

    // Phone
    html += '<div class="info-item"><span class="info-label">Phone Number</span><span class="info-value">';
    if (data.phone) {
        html += '<div style="display: flex; align-items: center;"><i class="fa fa-phone text-success" style="margin-right: 8px;"></i><span class="masked-content" data-real="' + realPhone + '" style="font-family: monospace; font-size: 15px; letter-spacing: 1px;">' + maskedPhone + '</span><a href="#" class="toggle-mask text-muted" style="margin-left: 10px; font-size: 14px;"><i class="fa fa-eye"></i></a></div>';
    } else {
        html += '<span class="empty">N/A</span>';
    }
    html += '</span></div>';

    // Email
    html += '<div class="info-item"><span class="info-label">Email Address</span><span class="info-value">';
    if (data.email) {
        html += '<a href="mailto:' + data.email + '">' + data.email + '</a>';
    } else {
        html += '<span class="empty">N/A</span>';
    }
    html += '</span></div>';

    // Address
    html += '<div class="info-item"><span class="info-label">Address</span><span class="info-value">' + (data.address ? data.address.replace(/\n/g, '<br>') : '<span class="empty">No address on file</span>') + '</span></div>';

    html += '</div></div></div>';

    // Other Details
    html += '<div class="col-md-6"><div class="overview-section" style="min-height: 220px;"><div class="overview-header"><i class="fa fa-info-circle" style="color: #7c3aed; background: #f5f3ff;"></i><h3>Other Information</h3></div><div class="info-grid" style="grid-template-columns: 1fr;">';
    html += '<div class="info-item"><span class="info-label">Reference (Referral Doctor)</span><span class="info-value">' + (data.referral_doc || '<span class="empty">None</span>') + '</span></div>';
    html += '<div class="info-item"><span class="info-label">Attender Name</span><span class="info-value">' + (data.attender || '<span class="empty">-</span>') + '</span></div>';
    html += '<div class="info-item"><span class="info-label">Registration Date</span><span class="info-value">' + (data.reg_date || '-') + '</span></div>';

    // Visits
    html += '<div class="info-item"><span class="info-label">Total Visits</span><span class="info-value"><span class="tag-badge tag-green">' + (data.visits || 0) + ' Visits</span></span></div>';

    // Groups
    if (groupBadges) {
        html += '<div class="info-item"><span class="info-label">Groups</span><span class="info-value">' + groupBadges + '</span></div>';
    }

    html += '</div></div></div>';

    html += '</div>'; // End Row
    return html;
}

// Initialize Notes DataTable for Modal
function initDataTableInline(patient_id) {
    var tableId = '.table-patient-notes';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    initDataTable(tableId, admin_url + 'patient_master_modal/patient_master_modal/notes_table/' + patient_id, undefined, undefined, undefined, [2, 'desc']);
}

// Initialize Prescriptions DataTable for Modal
function initPrescriptionTableInline(patient_id) {
    var tableId = '.table-patient-prescriptions';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    initDataTable(tableId, admin_url + 'patient_master_modal/patient_master_modal/prescriptions_table/' + patient_id, undefined, undefined, undefined, [0, 'desc']);
}

// Initialize Invoices DataTable for Modal
function initInvoicesTableInline(patient_id) {
    var tableId = '.table-patient-invoices';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    initDataTable(tableId, admin_url + 'patient_master_modal/patient_master_modal/invoices_table/' + patient_id, undefined, undefined, undefined, [0, 'desc']);
}

// Initialize Payments DataTable for Modal
function initPaymentsTableInline(patient_id) {
    var tableId = '.table-patient-payments';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    initDataTable(tableId, admin_url + 'patient_master_modal/patient_master_modal/payments_table/' + patient_id, undefined, undefined, undefined, [0, 'desc']);
}

// Initialize Expenses DataTable for Modal
function initExpensesTableInline(patient_id) {
    var tableId = '.table-patient-expenses';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    initDataTable(tableId, admin_url + 'patient_master_modal/patient_master_modal/expenses_table/' + patient_id, undefined, undefined, undefined, [3, 'desc']);
}

// Initialize Reminders DataTable for Modal
function initReminderTableInline(patient_id) {
    var tableId = '.table-patient-reminders';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    initDataTable(tableId, admin_url + 'patient_master_modal/patient_master_modal/reminders_table/' + patient_id, undefined, undefined, undefined, [1, 'desc']);
}

function save_note() {
    var description = $('#note_description').val();
    var status_id = $('#note_status_id').val();

    if (!description) {
        alert_float('warning', 'Please enter a call log.');
        return;
    }

    $.post(admin_url + 'patient_master_modal/patient_master_modal/add_note', {
        patient_id: current_patient_id,
        description: description,
        status_id: status_id,
        prescription_id: current_prescription_id
    }, function (response) {
        var result = JSON.parse(response);
        if (result.success) {
            $('#note_description').val(''); // Clear textarea
            $('#note_status_id').selectpicker('val', ''); // Reset status
            // Reload DataTable
            $('.table-patient-notes').DataTable().ajax.reload();
        } else {
            alert_float('danger', 'Error capturing call log: ' + (result.message || 'Unknown error'));
        }
    });
}

function save_reminder() {
    var date = $('#reminder_date').val();
    var staff = $('#reminder_staff').val();
    var description = $('#reminder_description').val();
    var notify_by_email = $('#reminder_notify_by_email').prop('checked') ? 1 : 0;

    if (!date) { alert('Date is required'); return; }
    if (!staff) { alert('Staff is required'); return; }
    if (!description) { alert('Description is required'); return; }

    $.post(admin_url + 'patient_master_modal/patient_master_modal/add_reminder', {
        patient_id: current_patient_id,
        date: date,
        staff: staff,
        description: description,
        notify_by_email: notify_by_email
    }, function (response) {
        var result = JSON.parse(response);
        if (result.success) {
            $('#reminder_description').val('');
            // Reload DataTable
            $('.table-patient-reminders').DataTable().ajax.reload();
            alert_float('success', result.message);
        } else {
            alert_float('danger', result.message);
        }
    });
}

function delete_reminder(id) {
    if (confirm('Are you sure you want to delete this reminder?')) {
        $.get(admin_url + 'patient_master_modal/patient_master_modal/delete_reminder/' + id, function (response) {
            var result = JSON.parse(response);
            if (result.success) {
                $('.table-patient-reminders').DataTable().ajax.reload();
                alert_float('success', result.message);
            } else {
                alert_float('danger', result.message || 'Error deleting reminder');
            }
        });
    }
}

// Initialize Files DataTable for Modal
function initFilesTableInline(patient_id) {
    var tableId = '.table-patient-files';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    initDataTable(tableId, admin_url + 'patient_master_modal/patient_master_modal/files_table/' + patient_id, undefined, undefined, undefined, [2, 'desc']);
}

function delete_file_inline(id) {
    if (confirm('Are you sure you want to delete this file?')) {
        $.get(admin_url + 'patient_master_modal/patient_master_modal/delete_file/' + id, function (response) {
            var result = JSON.parse(response);
            if (result.success) {
                $('.table-patient-files').DataTable().ajax.reload();
                alert_float('success', result.message);
            } else {
                alert_float('danger', result.message || 'Error deleting file');
            }
        });
    }
}

function updateFileLabel(input) {
    if (!input) return;
    if (input.files && input.files.length > 0) {
        $('#dropzone_content').hide();
        $('#file_selected_info').show();
        var text = input.files.length + ' file' + (input.files.length !== 1 ? 's' : '') + ' selected';
        $('#file_selected_count').text(text);
    } else {
        $('#dropzone_content').show();
        $('#file_selected_info').hide();
    }
}


// Initialize Tasks DataTable for Modal
function initTaskTableInline(patient_id) {
    var tableId = '.table-patient-tasks';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    initDataTable(tableId, admin_url + 'patient_master_modal/patient_master_modal/tasks_table/' + patient_id, undefined, undefined, undefined, [2, 'asc']);
}

function save_task() {
    var name = $('#task_name').val();
    var startdate = $('#task_startdate').val();
    var duedate = $('#task_duedate').val();
    var priority = $('#task_priority').val();

    if (!name) { alert('Subject is required'); return; }
    if (!startdate) { alert('Start Date is required'); return; }

    $.post(admin_url + 'patient_master_modal/patient_master_modal/add_task', {
        patient_id: current_patient_id,
        name: name,
        startdate: startdate,
        duedate: duedate,
        priority: priority
    }, function (response) {
        var result = JSON.parse(response);
        if (result.success) {
            $('#task_name').val('');
            $('#task_duedate').val('');
            // Reset priority to medium
            $('#task_priority').selectpicker('val', '2');

            // Reload DataTable
            $('.table-patient-tasks').DataTable().ajax.reload();
            alert_float('success', result.message);
        } else {
            alert_float('danger', result.message);
        }
    });
}

function delete_task_inline(id) {
    if (confirm('Are you sure you want to delete this task?')) {
        $.get(admin_url + 'patient_master_modal/patient_master_modal/delete_task/' + id, function (response) {
            var result = JSON.parse(response);
            if (result.success) {
                $('.table-patient-tasks').DataTable().ajax.reload();
                alert_float('success', result.message);
            } else {
                alert_float('danger', result.message || 'Error deleting task');
            }
        });
    }
}

// Initialize Tickets DataTable for Modal
function initTicketsTableInline(patient_id) {
    var tableId = '.table-tickets-single';
    if ($.fn.dataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    $(tableId).find('#th-submitter').removeClass('toggleable');
    initDataTable(tableId, admin_url + 'tickets/index/false/' + patient_id, undefined, undefined, undefined, [$(tableId).find('thead .ticket_created_column').index(), 'desc']);
}
