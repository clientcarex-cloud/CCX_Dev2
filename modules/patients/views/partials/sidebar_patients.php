<div class="panel_s" style="height: calc(100vh - 120px); overflow-y: auto;">
    <div class="panel-heading">
        <span class="fa fa-stethoscope"></span> <?php echo _l('Patients List'); ?>
        <div class="mtop10">
            <div class="input-group">
                <input type="text" class="form-control datepicker" id="sidebar_date_filter"
                    value="<?php echo _d(date('Y-m-d')); ?>">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
            </div>
            <div class="input-group mtop5">
                <input type="text" class="form-control" id="sidebar_patient_search" placeholder="Search...">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
            </div>
        </div>
    </div>
    <div class="panel-body" style="padding: 0;">
        <ul class="list-group" id="sidebar_patients_list" style="margin-bottom: 0;">
            <!-- AJAX Content Here -->
            <li class="list-group-item text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</li>
        </ul>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        init_datepicker();
        load_sidebar_patients();

        // Date Change event
        $('#sidebar_date_filter').on('change', function () {
            load_sidebar_patients();
        });

        var searchTimeout_sidebar;
        $('#sidebar_patient_search').on('input', function () {
            clearTimeout(searchTimeout_sidebar);
            load_sidebar_patients();
        });

        function load_sidebar_patients() {
            var search = $('#sidebar_patient_search').val();
            var date = $('#sidebar_date_filter').val();

            $.get(admin_url + 'patients/visits/get_sidebar_patients', { search: search, date: date }, function (response) {
                $('#sidebar_patients_list').html(response);
            });
        }
    });
</script>