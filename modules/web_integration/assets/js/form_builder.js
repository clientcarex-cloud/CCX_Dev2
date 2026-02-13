function generate_secret_key() {
    var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var length = 32;
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    $('#secret_key').val(result);
}

$(function () {
    // Form Category Logic
    $('#form_category').on('change', function () {
        var category = $(this).val();

        if (category == 'Lead') {
            // Determine visibility
            $('label[for="link_with_leads"]').closest('.form-group').show();
            $('label[for="link_with_appointments"]').closest('.form-group').hide();

            // Set Values
            $('input[name="link_with_leads"][value="1"]').prop('checked', true).trigger('change');
            $('input[name="link_with_appointments"][value="0"]').prop('checked', true);

        } else if (category == 'Appointment') {
            // Determine visibility
            $('label[for="link_with_leads"]').closest('.form-group').hide();
            $('label[for="link_with_appointments"]').closest('.form-group').show();

            // Set Values
            $('input[name="link_with_leads"][value="0"]').prop('checked', true).trigger('change');
            $('input[name="link_with_appointments"][value="1"]').prop('checked', true);

        } else if (category == 'Lead + Appointment') {
            // Determine visibility
            $('label[for="link_with_leads"]').closest('.form-group').show();
            $('label[for="link_with_appointments"]').closest('.form-group').show();

            // Set Values
            $('input[name="link_with_leads"][value="1"]').prop('checked', true).trigger('change');
            $('input[name="link_with_appointments"][value="1"]').prop('checked', true);
        }
    });

    // Trigger on load to set initial state / UI, but respect existing values unless changed
    // For new forms, this sets default. For editing, checking current category sets regular visibility.
    // We trigger change to enforce rules based on current category selection.
    $('#form_category').trigger('change');

    $('input[name="link_with_leads"]').on('change', function () {
        if ($(this).val() == 1) {
            $('#lead_settings_wrapper').removeClass('hide');
        } else {
            $('#lead_settings_wrapper').addClass('hide');
        }
    });
});
