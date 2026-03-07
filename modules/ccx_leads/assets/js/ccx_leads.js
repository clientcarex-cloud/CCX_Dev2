
function ccx_switch_view(view) {
    if (view === 'list') {
        $('#ccx_kanban_view').addClass('hide');
        $('#ccx_list_view').removeClass('hide');
        // Update ARIA on view buttons
        $('.btn-group[aria-label="View mode"] button[aria-label="List view"]').attr('aria-pressed', 'true');
        $('.btn-group[aria-label="View mode"] button[aria-label="Kanban view"]').attr('aria-pressed', 'false');
    } else {
        $('#ccx_list_view').addClass('hide');
        $('#ccx_kanban_view').removeClass('hide');
        // Update ARIA on view buttons
        $('.btn-group[aria-label="View mode"] button[aria-label="List view"]').attr('aria-pressed', 'false');
        $('.btn-group[aria-label="View mode"] button[aria-label="Kanban view"]').attr('aria-pressed', 'true');
    }
}


function ccx_lead_profile(id) {
    // Use a unique modal ID (#ccx-lead-modal) so the core Perfex CRM JS
    // (which owns #lead-modal) cannot intercept or replace our content.
    if ($('#ccx-lead-modal').length === 0) {
        $('body').append(
            '<div class="modal fade" id="ccx-lead-modal" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog modal-lg" role="document">' +
            '<div class="modal-content"></div>' +
            '</div></div>'
        );
    }
    // Load module's own lead modal — completely independent from core Perfex CRM
    requestGet('ccx_leads/lead_modal/' + id).done(function (html) {
        $('#ccx-lead-modal .modal-content').html(html);
        $('#ccx-lead-modal').modal('show');
    }).fail(function (error) {
        alert_float('danger', error.responseText || 'Failed to load lead data.');
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

    // Apply field settings from CCX Leads settings when the core modal opens
    $(document).off('shown.bs.modal.ccx_field_settings').on('shown.bs.modal.ccx_field_settings', '#lead-modal', function () {
        var $modal = $(this);

        if (typeof ccx_field_settings === 'undefined' || !ccx_field_settings) {
            return;
        }

        $.each(ccx_field_settings, function (slug, setting) {
            var $field, $container, $label;

            // Special cases: tags, is_public, status, source, assigned
            if (slug === 'tags') {
                $container = $modal.find('#inputTagsWrapper').closest('.col-md-12');
                $label = $modal.find('#inputTagsWrapper label');
            } else if (slug === 'is_public') {
                $container = $modal.find('input[name="is_public"]').closest('.checkbox');
                $label = $container.find('label');
            } else if (slug === 'status') {
                $container = $modal.find('select[name="status"]').closest('.col-md-4');
                $label = $container.find('label');
            } else if (slug === 'source') {
                $container = $modal.find('select[name="source"]').closest('.col-md-4');
                $label = $container.find('label');
            } else if (slug === 'assigned') {
                $container = $modal.find('select[name="assigned"]').closest('.col-md-4');
                $label = $container.find('label');
            } else if (slug === 'country') {
                $container = $modal.find('select[name="country"]').closest('.form-group');
                $label = $container.find('label');
            } else if (slug === 'address') {
                $container = $modal.find('textarea[name="address"]').closest('.form-group');
                $label = $container.find('label');
            } else if (slug === 'description') {
                $container = $modal.find('textarea[name="description"]').closest('.form-group');
                $label = $container.find('label');
            } else if (slug === 'lead_value') {
                $container = $modal.find('input[name="lead_value"]').closest('.form-group');
                $label = $container.find('label');
            } else {
                // Standard input fields: name, title, email, phonenumber, website, company, city, state, zip
                $field = $modal.find('input[name="' + slug + '"]');
                if ($field.length === 0) {
                    $field = $modal.find('[name="' + slug + '"]');
                }
                $container = $field.closest('.form-group');
                $label = $container.find('label');
            }

            if (!$container || $container.length === 0) {
                return; // skip if container not found
            }

            // 1. Hide if inactive
            if (setting.active == 0) {
                $container.hide();
                // Also hide surrounding hr separators for tags
                if (slug === 'tags') {
                    $container.prev('hr').hide();
                    $container.next('.clearfix').next('hr').hide();
                }
                return; // No need to change label/required for hidden fields
            }

            // 2. Custom label
            if (setting.label && $label.length > 0) {
                // Preserve any existing icons inside the label
                var $icon = $label.find('i, .fa, .fas, .far, .fab');
                if ($icon.length > 0) {
                    $label.contents().filter(function () {
                        return this.nodeType === 3; // text nodes only
                    }).first().replaceWith(' ' + setting.label);
                } else {
                    $label.text(setting.label);
                }
            }

            // 3. Required
            if (setting.required == 1) {
                var $input = $container.find('input, select, textarea').first();
                if ($input.length > 0 && !$input.attr('required')) {
                    $input.attr('required', true);
                }
                // Add asterisk to label if not already present
                if ($label.length > 0 && $label.find('.req').length === 0) {
                    $label.append(' <span class="req text-danger">*</span>');
                }
            }
        });

        // Unbind after first use to avoid stacking
        $(document).off('shown.bs.modal.ccx_field_settings');
    });
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

    // Custom edit toggle for CCX lead modal (replaces core [lead-edit] handler)
    $('body').on('click', '[ccx-lead-edit]', function (e) {
        e.preventDefault();
        var $modal = $('#ccx-lead-modal');
        var $leadEdit = $modal.find('.lead-edit');
        $modal.find('.lead-view').toggleClass('hide');
        $leadEdit.toggleClass('hide');

        // Initialize UI components when entering edit mode
        if (!$leadEdit.hasClass('hide')) {
            init_selectpicker();
            init_datepicker();
            init_tags_inputs();
            init_color_pickers();
            validate_lead_form();
            var $address = $modal.find('#address');
            if ($address.length > 0 && $address.is('textarea')) {
                var scrollHeight = $address[0].scrollHeight;
                $address.height(0).height(scrollHeight - 15);
                $address.css('padding-top', '9px');
            }
        }
    });

    // Auto-populate Company field from Name field and hide Company field
    $('body').on('shown.bs.modal', '#ccx-lead-modal', function (e) {
        // Check if we are in the CCX Leads module context
        if ($('.table-ccx-leads').length > 0 || $('#ccx_leads_view_wrapper').length > 0) {
            var $companyInput = $('#ccx-lead-modal input[name="company"]');
            var $nameInput = $('#ccx-lead-modal input[name="name"]');

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

        // Initialize Perfex UI components for the loaded modal content
        init_selectpicker();
        init_datepicker();
        init_tags_inputs();
        init_color_pickers();
    });

    // Keyboard support for status filter (Enter / Space)
    $('body').on('keydown', '.ccx-status-filter-item', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });
});
