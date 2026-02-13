<?php init_head(); ?>
<script type="text/javascript" src="https://unpkg.com/webcam-easy/dist/webcam-easy.min.js"></script>
<link rel="stylesheet" type="text/css" id="facelogin-css" href="<?php echo module_dir_url('facelogin', 'assets/css/face-detection.css?v=1'); ?>">

<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-4xl tw-mx-auto">
            <input id="userid" value="<?= get_staff_user_id() ?>" type="hidden">
            <div class="panel_s">
                <div class="panel-body">

                    <div class="row">

                        <div class="col-12 align-top" id="webcam-container">
                            <div class="loading d-none">
                                Loading Model
                                <div class="spinner-border" role="status">
                                    <span class="sr-only"></span>
                                </div>
                            </div>

                            <div id="video-container">
                                <video id="webcam" poster="" autoplay muted playsinline></video>
                            </div>
                            <div id="errorMsg" class="col-12 alert-danger d-none">
                                Fail to start camera <br>
                                1. Please allow permission to access camera. <br>
                                2. If you are browsing through social media built in browsers, look for the ... or
                                browser icon on the right top/bottom corner, and open the page in Sarafi (iPhone)/
                                Chrome (Android)
                            </div>
                            <?php if($is_active == 1){ ?>
                            <p class="text-success p-2 text-center"> The face registration is already complete. If you would like to make  updates, please enable the webcam.</p>
                            <?php }else{ ?>
                            <p class="text-danger p-2 text-center"> The face feature is currently unavailable. If you would like to enable it, please turn on your webcam.
                            </p>
                            <?php } ?>

                        </div>
                    </div>
                </div>
                <div class="panel-footer ">
                    <div class="row">
                        <div class="text-left col-md-6 col-sm-6 col-xs-12 text-center-xs ">
                            <div>
                                <label class="form-switch">
                                    <input type="checkbox" id="webcam-switch">
                                    <i></i>
                                    <span>Webcam</span>
                                </label>
                                <button id="cameraFlip" class="btn d-none">
                                </button>
                            </div>
                            <div class="d-none">
                                <input type="checkbox" id="detection-switch" cheke>
                            </div>
                            <div class="d-none">
                                <input type="checkbox" id="box-switch" cheke>
                            </div>
                        </div>
                        <div class="text-right col-md-6 col-sm-6  col-xs-12 text-center-xs ">
                            <?php  if($is_active == 0){ ?>
                            <button class="btn btn-primary" type="button" id="save-face"><?= _l('Save') ?></button>
                            <?php }else{ ?>
                                <a class="btn btn-danger" href="<?=admin_url('facelogin/remove_face')?>"><?= _l('Remove Face') ?></a>
                            <button class="btn btn-primary" type="button" id="save-face"><?= _l('Update') ?></button>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>
<script src="<?php echo module_dir_url('facelogin', 'assets/js/face-api.min.js'); ?>"></script>
<script src="<?php echo module_dir_url('facelogin', 'assets/js/face-detection.js'); ?>"></script>

<script>

    var save_url = '<?=admin_url('facelogin/save_face')?>';

    $('#save-face').on('click', async () => {
        $(".loading").removeClass('d-none');

        // Load the correct models before inference
        await Promise.all([
            faceapi.nets.tinyFaceDetector.load(modelPath),
            faceapi.nets.faceLandmark68TinyNet.load(modelPath), // 👈 you are using the tiny model
            faceapi.nets.faceRecognitionNet.load(modelPath) // 👈 needed for descriptor
        ]);

        const detection = await faceapi
            .detectSingleFace(webcamElement, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks(true) // ✅ use true for TinyNet
            .withFaceDescriptor();

        $(".loading").addClass('d-none');

        if (!detection) return alert("No face detected!");

        const descriptor = Array.from(detection.descriptor);
        var userid = $("#userid").val();
        const snapshot = captureSnapshot();

        const fd = new FormData();
        const csrf = (typeof csrfData !== 'undefined') ? csrfData : {token_name: 'csrf_token_name', hash: $('input[name="csrf_token_name"]').val()};
        fd.append('user_id', userid);
        fd.append('face_json', JSON.stringify(descriptor));
        fd.append('face_image', snapshot);
        if (csrf && csrf.token_name && csrf.hash) {
            fd.append(csrf.token_name, csrf.hash);
        }

        $.ajax({
            url: save_url,
            method: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                alert_float('success', 'Face saved!');
                toggleContrl("webcam-switch", false);
                setTimeout(() => {
                    location.reload();  
                }, 1000);
            },
            error: function(xhr, status, error) {
                console.error('Save failed:', error);
                alert_float('warning', 'Error saving face data.')
            }
        });
    });

    function captureSnapshot() {
        try {
            const shot = webcam.snap();
            if (shot) {
                return shot;
            }
        } catch (e) {
            // fallback to canvas
        }
        const canvas = document.createElement('canvas');
        canvas.width = webcamElement.videoWidth || 640;
        canvas.height = webcamElement.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(webcamElement, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/png');
    }
</script>
</body>

</html>
