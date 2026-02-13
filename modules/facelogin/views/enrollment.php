<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" type="text/css" id="facelogin-css" href="<?php echo module_dir_url('facelogin', 'assets/css/face-detection.css?v=3'); ?>">
<style>
    /* Keep enrollment forms consistent with Perfex styling */
    .facelogin-enroll .form-control,
    .facelogin-enroll .dataTables_filter input,
    .facelogin-enroll .dataTables_length select {
        height: auto;
        padding: 8px 12px;
        margin-bottom: 0;
        border: 1px solid #d5d7de;
        border-radius: 8px;
        background: #f9fafb;
    }
    .facelogin-enroll .bootstrap-select .btn {
        height: auto;
        padding: 8px 12px;
        border: 1px solid #d5d7de;
        background: #fff;
        box-shadow: none;
        border-radius: 8px;
    }
    .facelogin-enroll .bootstrap-select .btn:focus,
    .facelogin-enroll .bootstrap-select .btn:active,
    .facelogin-enroll .bootstrap-select .dropdown-toggle:focus {
        outline: none !important;
        box-shadow: none;
    }
    .facelogin-enroll .bootstrap-select .filter-option {
        display: flex;
        align-items: center;
    }
    .facelogin-enroll .dataTables_filter label,
    .facelogin-enroll .dataTables_length label {
        font-weight: 400;
    }
    .facelogin-enroll .filter-card {
        background: linear-gradient(135deg, #eef2ff, #f8fafc);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 8px 18px rgba(30, 41, 59, 0.05);
    }
    .facelogin-enroll .filter-card h4 {
        margin: 0 0 6px 0;
        font-weight: 600;
        color: #1e293b;
    }
    .facelogin-enroll .pill {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .2px;
    }
    .facelogin-enroll .pill-success { background: #ecfdf3; color: #16a34a; border: 1px solid #bbf7d0; }
    .facelogin-enroll .pill-warning { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .facelogin-enroll .pill-muted   { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
    .facelogin-enroll .table-facelogin>thead>tr>th {
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #0f172a;
        font-weight: 600;
    }
    .facelogin-enroll .table-facelogin>tbody>tr:hover {
        background: #f9fafb;
    }
    .facelogin-enroll .table-facelogin td {
        vertical-align: middle;
    }
    .facelogin-enroll .btn-xs {
        border-radius: 20px;
        padding: 5px 10px;
    }
    .facelogin-enroll .badge-dot {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }
    .facelogin-enroll .badge-dot:before {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
    }
    .facelogin-enroll .badge-active:before { background: #16a34a; }
    .facelogin-enroll .badge-inactive:before { background: #c2410c; }
</style>
<div id="wrapper" class="facelogin-enroll">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <div class="filter-card">
                    <div class="row">
                        <div class="col-md-8 col-sm-7">
                            <h4 class="tw-mt-0"><?php echo _l('facelogin_enrollment'); ?></h4>
                            <p class="text-muted m-b-0">Find staff, enroll faces, update snapshots, or clean up records in one place.</p>
                        </div>
                        <div class="col-md-4 col-sm-5 text-right mtop15-xs">
                            <a href="<?php echo admin_url('facelogin/setup'); ?>" class="btn btn-primary"><i class="fa fa-user"></i> My Face Setup</a>
                        </div>
                    </div>
                    <div class="row mtop20">
                        <div class="col-md-3 col-sm-6 mtop10-xs">
                            <label class="control-label text-muted">Search Name</label>
                            <input type="text" class="form-control filter-input" id="filter-name" placeholder="Search staff">
                        </div>
                        <div class="col-md-3 col-sm-6 mtop10-xs">
                            <label class="control-label text-muted">Role</label>
                            <select class="form-control selectpicker filter-input" data-width="100%" id="filter-role">
                                <option value="">All roles</option>
                                <?php foreach ($roles_list as $role) { ?>
                                    <option value="<?php echo html_escape($role); ?>"><?php echo html_escape($role); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mtop10-xs">
                            <label class="control-label text-muted">Department</label>
                            <select class="form-control selectpicker filter-input" data-width="100%" id="filter-dept">
                                <option value="">All departments</option>
                                <?php foreach ($dept_list as $dept) { ?>
                                    <option value="<?php echo html_escape($dept); ?>"><?php echo html_escape($dept); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mtop10-xs">
                            <label class="control-label text-muted">FaceLogin Status</label>
                            <select class="form-control selectpicker filter-input" data-width="100%" id="filter-status">
                                <option value="">All</option>
                                <option value="on">Registered</option>
                                <option value="off">Not Registered</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mtop15">
                    <table class="table dt-table table-striped table-facelogin" id="facelogin-staff-table">
                        <thead>
                        <tr>
                            <th style="width:60px;">S.No</th>
                            <th>Staff Name</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Staff Active</th>
                            <th>FaceLogin Status</th>
                            <th style="width:300px;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; foreach ($staff_list as $member) { ?>
                            <tr data-name="<?php echo strtolower(html_escape($member['full_name'] ?? '')); ?>"
                                data-role="<?php echo strtolower(html_escape($member['role_name'] ?? '')); ?>"
                                data-dept="<?php echo strtolower(html_escape($member['departments'] ?? '')); ?>"
                                data-status="<?php echo $member['has_face'] ? 'on' : 'off'; ?>">
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <span class="bold"><?php echo html_escape($member['full_name']); ?></span><br>
                                    <small class="text-muted"><?php echo html_escape($member['email']); ?></small>
                                </td>
                                <td><?php echo html_escape($member['role_name']); ?></td>
                                <td><?php echo html_escape($member['departments']); ?></td>
                                <td>
                                    <?php if (!empty($member['staff_active'])) { ?>
                                        <span class="badge-dot badge-active">Active</span>
                                    <?php } else { ?>
                                        <span class="badge-dot badge-inactive">Inactive</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($member['has_face']) { ?>
                                        <span class="pill pill-success">Registered</span>
                                    <?php } else { ?>
                                        <span class="pill pill-warning">Not Registered</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Actions">
                                        <?php if (!$member['has_face']) { ?>
                                            <button class="btn btn-success btn-xs action-add" data-staff="<?php echo $member['staffid']; ?>" data-name="<?php echo html_escape($member['full_name']); ?>"><i class="fa fa-plus"></i> Add</button>
                                        <?php } ?>
                                        <button class="btn btn-default btn-xs action-update" data-staff="<?php echo $member['staffid']; ?>" data-name="<?php echo html_escape($member['full_name']); ?>"><i class="fa fa-refresh"></i> Update</button>
                                        <?php if ($member['has_face']) { ?>
                                            <button class="btn btn-danger btn-xs action-remove" data-staff="<?php echo $member['staffid']; ?>" data-name="<?php echo html_escape($member['full_name']); ?>"><i class="fa fa-trash"></i> Remove</button>
                                            <?php if (is_admin()) { ?>
                                                <button class="btn btn-info btn-xs action-view" data-staff="<?php echo $member['staffid']; ?>" data-name="<?php echo html_escape($member['full_name']); ?>"><i class="fa fa-eye"></i> View</button>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Face capture modal -->
<div class="modal fade" id="faceEnrollModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="face-modal-title">Enroll Face</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mtop0">Align the face inside the frame, then click Save.</div>
                <div class="text-center mtop10">
                    <div id="face-model-loading" class="text-center m-b-10 hidden">
                        <i class="fa fa-spinner fa-spin"></i> Loading face detection model...
                    </div>
                    <video id="face-enroll-webcam" autoplay muted playsinline style="width:100%; border-radius:6px; background:#111;"></video>
                    <p class="m-t-10" id="face-enroll-status">Camera initializing...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-primary" id="enroll-save-btn"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Face view modal -->
<div class="modal fade" id="faceViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="face-view-title">Face Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="m-b-0"><strong>Unique ID:</strong> <span id="face-view-uniq"></span></p>
                <p class="m-b-0"><strong>Created:</strong> <span id="face-view-created"></span></p>
                <p class="m-b-10"><strong>Updated:</strong> <span id="face-view-updated"></span></p>
                <p class="text-muted m-b-10"><strong>Last captured:</strong> <span id="face-view-last-captured"></span></p>
                <ul class="list-unstyled small text-muted m-b-10" id="face-view-meta">
                    <li><strong>Descriptor length:</strong> <span id="face-view-descriptor-len">-</span></li>
                    <li><strong>Matching threshold:</strong> 0.6 (Euclidean distance)</li>
                    <li><strong>Image size:</strong> <span id="face-view-size">-</span></li>
                </ul>
                <div class="text-center m-b-10" id="face-view-image-wrapper" style="min-height:180px;">
                    <img id="face-view-image" src="" alt="Face snapshot" style="max-width:100%; max-height:320px; border-radius:6px; display:none; border:1px solid #e5e7eb; padding:6px;">
                    <p id="face-view-noimage" class="text-muted">No stored snapshot available.</p>
                </div>
                <div id="face-view-error" class="text-danger m-t-5"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script type="text/javascript" src="https://unpkg.com/webcam-easy/dist/webcam-easy.min.js"></script>
<script src="<?php echo module_dir_url('facelogin', 'assets/js/face-api.min.js'); ?>"></script>
<script>
    (function(){
        const modelPath = '<?php echo rtrim(module_dir_url('facelogin', 'assets/models'), '/'); ?>';
        const saveFaceUrl = '<?php echo admin_url('facelogin/save_face'); ?>';
        const removeFaceUrl = '<?php echo admin_url('facelogin/remove_face'); ?>';
        const viewFaceUrl = '<?php echo admin_url('facelogin/view_face'); ?>';
        const webcamElement = document.getElementById('face-enroll-webcam');
        const webcam = new Webcam(webcamElement, 'user');
        let modelsLoaded = false;
        let currentStaffId = null;
        let currentStaffName = '';

        function applyFilters() {
            const name = $('#filter-name').val().toLowerCase();
            const role = $('#filter-role').val().toLowerCase();
            const dept = $('#filter-dept').val().toLowerCase();
            const status = $('#filter-status').val();

            $('#facelogin-staff-table tbody tr').each(function() {
                const row = $(this);
                const rName = row.data('name') || '';
                const rRole = row.data('role') || '';
                const rDept = row.data('dept') || '';
                const rStatus = row.data('status') || '';

                const matches = (!name || rName.indexOf(name) !== -1)
                    && (!role || rRole === role)
                    && (!dept || rDept.indexOf(dept) !== -1)
                    && (!status || rStatus === status);
                row.toggle(matches);
            });
        }

        async function loadFaceModels() {
            if (modelsLoaded) return;
            $('#face-model-loading').removeClass('hidden');
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(modelPath),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(modelPath),
                    faceapi.nets.faceRecognitionNet.loadFromUri(modelPath)
                ]);
                modelsLoaded = true;
                $('#face-enroll-status').text('Align face in frame and click Save.');
            } catch (err) {
                console.error(err);
                $('#face-enroll-status').text('Unable to load models. Check network and try again.');
            } finally {
                $('#face-model-loading').addClass('hidden');
            }
        }

        function startCamera() {
            webcam.start().then(() => {
                $('#face-enroll-status').text('Align face in frame and click Save.');
            }).catch(err => {
                console.error(err);
                $('#face-enroll-status').text('Unable to start camera. Please allow access.');
            });
        }

        async function captureAndSaveFace() {
            if (!currentStaffId) return;
            $('#face-enroll-status').text('Detecting face...');

            await loadFaceModels();

            try {
                const detection = await faceapi
                    .detectSingleFace(webcamElement, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks(true)
                    .withFaceDescriptor();

                if (!detection) {
                    $('#face-enroll-status').text('No face detected. Please try again.');
                    return;
                }

                $('#face-enroll-status').text('Saving...');

                const snapshot = captureSnapshot();

                const fd = new FormData();
                const csrf = (typeof csrfData !== 'undefined') ? csrfData : {token_name: 'csrf_token_name', hash: $('input[name="csrf_token_name"]').val()};
                fd.append('user_id', currentStaffId);
                fd.append('face_json', JSON.stringify(Array.from(detection.descriptor)));
                fd.append('face_image', snapshot);
                if (csrf && csrf.token_name && csrf.hash) {
                    fd.append(csrf.token_name, csrf.hash);
                }

                $.ajax({
                    url: saveFaceUrl,
                    method: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(res) {
                        if (res && res.status) {
                            alert_float('success', 'Face saved for ' + currentStaffName);
                        } else {
                            alert_float('warning', res && res.message ? res.message : 'Could not save face.');
                        }
                        setTimeout(() => location.reload(), 700);
                    },
                    error: function() {
                        $('#face-enroll-status').text('Error while saving. Please retry.');
                    }
                });
            } catch (err) {
                console.error(err);
                $('#face-enroll-status').text('Error detecting face. Please retry.');
            }
        }

        function removeFace(staffId, staffName) {
            if (!confirm('Remove saved face data for ' + staffName + '?')) return;
            $.post(removeFaceUrl + '/' + staffId, {
                csrf_token_name: $('input[name="csrf_token_name"]').val()
            }, function(res) {
                try {
                    const data = typeof res === 'string' ? JSON.parse(res) : res;
                    if (data.status === 'success') {
                        alert_float('success', 'Face data removed for ' + staffName);
                        setTimeout(() => location.reload(), 600);
                    } else {
                        alert_float('warning', data.message || 'Could not remove face data.');
                    }
                } catch (e) {
                    alert_float('warning', 'Could not remove face data.');
                }
            });
        }

        function captureSnapshot() {
            try {
                const shot = webcam.snap();
                if (shot) {
                    return shot;
                }
            } catch (e) {
                // fall back
            }
            const canvas = document.createElement('canvas');
            canvas.width = webcamElement.videoWidth || 640;
            canvas.height = webcamElement.videoHeight || 480;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(webcamElement, 0, 0, canvas.width, canvas.height);
            return canvas.toDataURL('image/png');
        }

        $(function() {
            $('.selectpicker').selectpicker();

            $('.filter-input').on('input change', applyFilters);

            $('body').on('click', '.action-add', function() {
                currentStaffId = $(this).data('staff');
                currentStaffName = $(this).data('name');
                $('#face-modal-title').text('Add Face - ' + currentStaffName);
                $('#face-enroll-status').text('Camera initializing...');
                $('#faceEnrollModal').modal('show');
            });
            $('body').on('click', '.action-update', function() {
                currentStaffId = $(this).data('staff');
                currentStaffName = $(this).data('name');
                $('#face-modal-title').text('Update Face - ' + currentStaffName);
                $('#face-enroll-status').text('Camera initializing...');
                $('#faceEnrollModal').modal('show');
            });
            $('body').on('click', '.action-remove', function() {
                removeFace($(this).data('staff'), $(this).data('name'));
            });
            $('body').on('click', '.action-view', function() {
                const staffId = $(this).data('staff');
                const staffName = $(this).data('name');
                $('#face-view-title').text('Face Details - ' + staffName);
                $('#face-view-error').text('');
                $('#face-view-uniq').text('');
                $('#face-view-created').text('');
                $('#face-view-updated').text('');
                $('#face-view-last-captured').text('');
                $('#face-view-descriptor-len').text('-');
                $('#face-view-size').text('-');
                $('#face-view-image').hide().attr('src', '');
                $('#face-view-noimage').show();
                $('#faceViewModal').modal('show');

                $.get(viewFaceUrl + '/' + staffId, function(res) {
                    if (!res || !res.status) {
                        $('#face-view-error').text(res && res.message ? res.message : 'Unable to load face data.');
                        return;
                    }
                    $('#face-view-uniq').text(res.uniq_id || '-');
                    $('#face-view-created').text(res.created_at || '-');
                    $('#face-view-updated').text(res.updated_at || '-');
                    $('#face-view-last-captured').text(res.updated_at || res.created_at || '-');
                    $('#face-view-descriptor-len').text(res.descriptor_len || '-');
                    $('#face-view-size').text(res.image_size ? (Math.round(res.image_size / 1024) + ' KB') : '-');
                    if (res.image_url) {
                        const versioned = res.image_version ? (res.image_url + '?v=' + res.image_version) : res.image_url + '?_=' + Date.now();
                        $('#face-view-image').attr('src', versioned).show();
                        $('#face-view-noimage').hide();
                    } else {
                        $('#face-view-noimage').text('No stored snapshot available.').show();
                    }
                }, 'json').fail(function() {
                    $('#face-view-error').text('Unable to load face data.');
                });
            });

            $('#faceEnrollModal').on('shown.bs.modal', function () {
                startCamera();
                loadFaceModels();
            });

            $('#faceEnrollModal').on('hidden.bs.modal', function () {
                webcam.stop();
            });

            $('#enroll-save-btn').on('click', captureAndSaveFace);
        });
    })();
</script>
</html>
