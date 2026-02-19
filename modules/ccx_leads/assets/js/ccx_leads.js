
function ccx_switch_view(view) {
    if (view === 'list') {
        $('#ccx_kanban_view').addClass('hide');
        $('#ccx_list_view').removeClass('hide');

        $('#btn-list-view').addClass('active');
        $('#btn-kanban-view').removeClass('active');
    } else {
        $('#ccx_list_view').addClass('hide');
        $('#ccx_kanban_view').removeClass('hide');

        $('#btn-kanban-view').addClass('active');
        $('#btn-list-view').removeClass('active');
    }
}

function ccx_lead_profile(id) {
    // Use core Perfex CRM lead modal (init_lead is in main.js globally)
    init_lead(id);
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
});
