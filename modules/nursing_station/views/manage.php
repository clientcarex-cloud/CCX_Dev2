<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <!-- Left Sidebar: Patients List -->
            <div class="col-md-3" style="padding-right: 0;">
                <div class="panel_s" style="height: calc(100vh - 120px); overflow-y: auto;">
                    <div class="panel-heading">
                        <span class="fa fa-stethoscope"></span> <?php echo _l('Patients List'); ?>
                        <div class="input-group mtop10">
                            <input type="text" class="form-control" id="search_patients" placeholder="Search...">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                    <div class="panel-body" style="padding: 0;">
                        <ul class="list-group" id="visit_list" style="margin-bottom: 0;">
                            <?php if (!empty($visits)) {
                                foreach ($visits as $visit) {
                                    $visit_time = strtotime($visit['created_at']);
                                    $date_display = date('d M Y, h:i A', $visit_time);
                                    ?>
                                    <li class="list-group-item visit-item" data-id="<?php echo $visit['id']; ?>"
                                        style="cursor: pointer;">
                                        <div style="font-weight: bold; color: #337ab7;"><?php echo $visit['patient_name']; ?>
                                        </div>
                                        <div style="font-size: 12px; color: #777;"><?php echo $date_display; ?></div>
                                        <div style="font-size: 11px;">
                                            <?php if ($visit['invoice_id']) {
                                                echo '<span class="text-success">Pay Status: Checking...</span>';
                                            } ?>
                                        </div>
                                    </li>
                                <?php }
                            } else { ?>
                                <li class="list-group-item text-center"><?php echo _l('No visits found'); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Content: Details -->
            <div class="col-md-9">
                <div class="panel_s" style="height: calc(100vh - 120px); overflow-y: auto;">
                    <div class="panel-body" id="visit_details_container">
                        <div class="text-center" style="padding-top: 100px; color: #777;">
                            <i class="fa fa-hospital-o fa-4x"></i>
                            <h3>Select a patient visit to view details</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        // Handle Visit Click
        $('.visit-item').click(function () {
            var visitId = $(this).data('id');
            $('.visit-item').removeClass('active').css('background-color', '');
            $(this).addClass('active').css('background-color', '#f0f5f9'); // Highlight

            // Show loading
            $('#visit_details_container').html('<div class="text-center" style="padding-top:50px;"><i class="fa fa-spinner fa-spin fa-3x"></i><br>Loading details...</div>');

            // Fetch Details
            $.getJSON('<?php echo admin_url("nursing_station/get_visit_details/"); ?>' + visitId, function (response) {
                if (response.success) {
                    $('#visit_details_container').html(response.html);
                    // Initialize Editor if needed (TinyMCE usually needs re-init on dynamic content)
                    if (typeof init_editor !== 'undefined') {
                        init_editor('.tinymce-editor', { height: 400 });
                    }
                } else {
                    $('#visit_details_container').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }).fail(function () {
                $('#visit_details_container').html('<div class="alert alert-danger">Error loading details.</div>');
            });
        });

        // Search Filter (Client-side)
        $('#search_patients').keyup(function () {
            var val = $(this).val().toLowerCase();
            $('.visit-item').each(function () {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(val) > -1);
            });
        });
    });
</script>