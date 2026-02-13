$(function () {
    // Branding Card Toggle
    $('#brandingTrigger').hover(
        function() { $('#healthoCard').stop(true, true).fadeIn(200); },
        function() { $('#healthoCard').stop(true, true).fadeOut(200); }
    );

    // Mobile/Click support
    $('#brandingTrigger').on('click', function(e) {
        e.stopPropagation(); // Prevent bubbling
        $('#healthoCard').fadeToggle(200);
    });

    $(document).on('click', function() {
        $('#healthoCard').fadeOut(200);
    });

    var linkWithAppointments = typeof web_integration_config !== 'undefined' ? web_integration_config.linkWithAppointments : false;
    var csrfName = typeof web_integration_config !== 'undefined' ? web_integration_config.csrfName : '';
    var csrfHash = typeof web_integration_config !== 'undefined' ? web_integration_config.csrfHash : '';
    var slotsUrl = typeof web_integration_config !== 'undefined' ? web_integration_config.slotsUrl : '';

    if (linkWithAppointments) {
        $('#doctor, #consultation_date').on('change', function () {
            var doctorId = $('#doctor').val();
            var date = $('#consultation_date').val();

            if (doctorId && date) {
                var data = {
                    doctor_id: doctorId,
                    date: date
                };
                if (csrfName) {
                    data[csrfName] = csrfHash;
                }

                $.post(slotsUrl, data, function (response) {
                    var slots = JSON.parse(response);
                    var html = '';
                    if (slots.length > 0) {
                        html += '<label>Available Slots:</label><div class="slots-container">';
                        $.each(slots, function (index, slot) {
                            var btnClass = 'slot-btn';
                            var style = '';

                            if (!slot.available) {
                                btnClass += ' disabled';
                            }

                            html += '<div class="' + btnClass + '" data-time="' + slot.start_time + '" data-end="' + slot.end_time + '" ' + style + '>' + slot.time + '</div>';
                        });
                        html += '</div>';
                    } else {
                        html = '<p class="text-warning">No slots available for this date.</p>';
                    }
                    $('#slots_wrapper').html(html);
                    $('#start_time').val(''); // Reset selected time
                    $('#end_time').val(''); // Reset end time
                }).fail(function (xhr, status, error) {
                    console.error("Error fetching slots:", status, error);
                    // console.log(xhr.responseText);
                    $('#slots_wrapper').html('<p class="text-danger">Error fetching slots. Please try again or contact support.</p>');
                });
            }
        });

        $(document).on('click', '.slot-btn', function () {
            if ($(this).hasClass('disabled')) return;

            $('.slot-btn').removeClass('active');
            $(this).addClass('active');

            $('#start_time').val($(this).data('time'));
            $('#end_time').val($(this).data('end')); // Set end time
            $('#slot_error').addClass('hide');
        });

        $('form').on('submit', function (e) {
            var startTime = $('#start_time').val();
            if (linkWithAppointments && !startTime) {
                e.preventDefault();
                $('#slot_error').removeClass('hide');
            }
        });
    }
});
