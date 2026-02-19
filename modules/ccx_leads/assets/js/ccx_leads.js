
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
    $.get(admin_url + 'ccx_leads/get_lead_data/' + id, function(response) {
        content.html(response);
    }).fail(function() {
         content.html('<div class="alert alert-danger">Failed to load lead data.</div>');
    });
}

function ccx_close_slideover() {
    $('#ccx_lead_slideover').removeClass('open');
    setTimeout(function(){
        $('#ccx_slideover_body').html(''); // Clear content after transition
    }, 300);
}

// Kanban drag and drop logic (Simplified wrapper around core or custom)
$(function() {
    // Bind click events on kanban cards if any
    $('body').on('click', '.kanban-card', function() {
        var id = $(this).data('lead-id');
        if (id) {
            ccx_lead_profile(id);
        }
    });
});
