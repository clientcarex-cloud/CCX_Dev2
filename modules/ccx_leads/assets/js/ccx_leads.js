
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
    var slideover = $('#ccx_lead_slideover');
    var content = $('#ccx_slideover_body');

    // Show slideover
    slideover.addClass('open');
    content.html('<div class="text-center ptop20"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');

    // Fetch data
    $.get(admin_url + 'ccx_leads/get_lead_data/' + id, function (response) {
        content.html(response);
    }).fail(function () {
        content.html('<div class="alert alert-danger">Failed to load lead data.</div>');
    });
}

function ccx_close_slideover() {
    $('#ccx_lead_slideover').removeClass('open');
    setTimeout(function () {
        $('#ccx_slideover_body').html(''); // Clear content after transition
    }, 300);
}

// Kanban drag and drop logic (Simplified wrapper around core or custom)
$(function () {
    // Bind click events on kanban cards if any
    $('body').on('click', '.kanban-card', function () {
        var id = $(this).data('lead-id');
        if (id) {
            ccx_lead_profile(id);
        }
    });
});

function ccx_leads_new_lead() {
    // Attempt to use core Perfex functionality if available
    if (typeof init_lead === 'function') {
        init_lead();
    } else {
        // Fallback: Redirect to core lead creation or alert
        // Checking if we can just open the modal manually or if we need to load dependencies
        alert('Standard Lead Modal requires core assets. Please ensure "init_lead" is available or implement custom form.');
        // Ideally we should have loaded core leads JS in the module init
    }
}

function delete_lead(id) {
    if (confirm('Are you sure you want to delete this lead?')) {
        $.get(admin_url + 'leads/delete/' + id, function (response) {
            // Assuming standard delete returns JSON or redirection
            // Reload view
            if ($('#ccx_list_view').hasClass('hide')) {
                // Refresh Kanban (reload page for now or re-fetch)
                window.location.reload();
            } else {
                $('.table-leads').DataTable().ajax.reload();
            }
            ccx_close_slideover();
        });
    }
}
