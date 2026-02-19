
function ccx_switch_view(view) {
    if (view === 'list') {
        $('#ccx_kanban_view').addClass('hide');
        $('#ccx_list_view').removeClass('hide');
    } else {
        $('#ccx_list_view').addClass('hide');
        $('#ccx_kanban_view').removeClass('hide');
    }
}


function ccx_lead_profile(id) {
    // Use module's own lead modal — independent from core Perfex CRM
    $.get(admin_url + 'ccx_leads/lead_modal/' + id, function(html) {
        $('#lead-modal .modal-content').html(html);
        $('#lead-modal').modal('show');
    });
}

function ccx_close_slideover() {
    $('#ccx_lead_slideover').removeClass('open');
    setTimeout(function () {
        $('#ccx_slideover_body').html('');
    }, 300);
}

function ccx_leads_new_lead() {
    // Use the core Perfex CRM lead modal
    init_lead();
}

function delete_lead(id) {
    if (confirm_delete()) {
        requestGetJSON('leads/delete/' + id).done(function (response) {
            if ($.fn.DataTable.isDataTable('.table-ccx-leads')) {
                ccx_leads_table.ajax.reload(null, false);
            } else {
                window.location.reload();
            }
            ccx_close_slideover();
        }).fail(function () {
            window.location.reload();
        });
    }
}

$(function () {
    // Kanban card click - open lead in the core Perfex CRM lead modal
    $('body').on('click', '.kanban-card', function () {
        var id = $(this).data('lead-id');
        if (id) {
            ccx_lead_profile(id);
        }
    });

    // Auto-populate Company field from Name field and hide Company field
    $('body').on('shown.bs.modal', '#lead-modal', function (e) {
        // Check if we are in the CCX Leads module context (or just apply globally if acceptable, 
        // but safest to check if the table exists or we are on the page)
        if ($('.table-ccx-leads').length > 0 || $('#ccx_leads_view_wrapper').length > 0) {
            var $companyInput = $('#lead-modal input[name="company"]');
            var $nameInput = $('#lead-modal input[name="name"]');

            if ($companyInput.length > 0) {
                // Hide the company input wrapper (usually .form-group)
                $companyInput.closest('.form-group').addClass('hide');

                // If creating new lead (company might be empty), sync it with name
                if ($nameInput.length > 0) {
                    // Initial sync if name already has value
                    if ($nameInput.val() && !$companyInput.val()) {
                        $companyInput.val($nameInput.val());
                    }

                    // Sync on change
                    $nameInput.on('input blur change', function () {
                        $companyInput.val($(this).val());
                    });
                }
            }
        }
    });
});
