<link rel="stylesheet" type="text/css" id="facelogin-css" href="<?php echo module_dir_url('facelogin', 'assets/css/face-detection-login.css'); ?>?v=2">

<!-- Face Login Button -->
<button type="button" onclick="showFaceSuccessModal()" id="facemodal_btn" class="btn btn-default btn-block mt-2">Login via Face</button>

<!-- Face ID Modal -->
<div id="face-id-modal" class="face-id-modal">
    <div class="face-id-box">
        <span class="face-id-close" onclick="hideFaceModal()">&times;</span>

        <h3>Face ID</h3>
        <!-- Live Webcam Video Feed -->
        <div class="face-video-container">
            <div style="position: relative; width: 240px; height: 180px;">
                <video id="webcam" autoplay muted playsinline width="150" height="200"
                    style="border-radius: 12px;"></video>
                <canvas id="face-canvas" width="150" height="180"
                    style="position: absolute; top: 0; left: 15px; pointer-events: none;"></canvas>
            </div>

        </div>
        <p class="face-id-status">Success</p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/webcam-easy/dist/webcam-easy.min.js"></script>
<script src="<?php echo module_dir_url('facelogin', 'assets/js/face-api.min.js'); ?>"></script>
<!-- Face ID Modal Script -->
<script>
    // Absolute endpoint so it works from the login page (alias route defined in my_routes.php)
    window.faceVerifyUrl = '<?php echo site_url('facelogin/faceid'); ?>';
</script>
<script src="<?php echo module_dir_url('facelogin', 'assets/js/face-detection-login.js'); ?>"></script>
