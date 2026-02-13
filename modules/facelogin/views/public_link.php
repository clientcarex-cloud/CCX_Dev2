<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Face Punch</title>
    <link rel="stylesheet" type="text/css"
        href="<?php echo module_dir_url('facelogin', 'assets/css/face-detection-login.css'); ?>?v=3">
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            background: radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.15), transparent 35%), radial-gradient(circle at 80% 10%, rgba(236, 72, 153, 0.15), transparent 30%), #0b1020;
            color: #e2e8f0;
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            margin: 0;
            padding: 0;
        }

        .facelink-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            position: relative;
        }

        .facelink-shell::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(148, 163, 184, 0.08), transparent 45%, rgba(94, 234, 212, 0.08));
            pointer-events: none;
        }

        .facelink-card {
            width: 100%;
            max-width: 640px;
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 25px 55px rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(12px);
            position: relative;
            overflow: hidden;
        }

        .facelink-card::after {
            content: '';
            position: absolute;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.18), transparent 60%);
            right: -30px;
            top: -30px;
            filter: blur(10px);
        }

        .facelink-header {
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
            padding-right: 40px;
        }

        .facelink-card h3 {
            margin: 6px 0 4px 0;
            font-weight: 700;
            color: #f8fafc;
            letter-spacing: 0.2px;
        }

        .facelink-card p {
            margin: 0 0 10px 0;
            color: #cbd5e1;
        }

        .video-wrap {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.3);
            background: #0b1224;
            aspect-ratio: 4 / 3;
        }

        #facelink-webcam {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .status-chip {
            position: absolute;
            left: 12px;
            bottom: 12px;
            background: rgba(15, 23, 42, 0.78);
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 700;
            letter-spacing: 0.3px;
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.5);
            backdrop-filter: blur(6px);
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(94, 234, 212, 0.12);
            border: 1px solid rgba(94, 234, 212, 0.45);
            color: #a5f3fc;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .success-card {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ffffff;
            border: 1px solid #22c55e;
            border-radius: 18px;
            padding: 24px;
            color: #334155;
            z-index: 10;
            width: 80%;
            max-width: 450px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .success-card.hidden {
            display: none;
        }

        .success-card h4 {
            margin: 0 0 10px 0;
            font-weight: 700;
            color: #15803d;
            font-size: 24px;
        }

        .meta {
            color: #000000;
            font-size: 18px;
            margin: 4px 0;
            line-height: 1.4;
        }

        .fullscreen-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #cbd5e1;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            z-index: 10;
        }

        .fullscreen-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        @media (max-width: 540px) {
            .facelink-shell {
                padding: 12px;
                align-items: center;
            }

            .facelink-card {
                padding: 14px;
                border-radius: 14px;
                margin: 0 auto;
            }

            .video-wrap {
                aspect-ratio: 3 / 4;
            }

            .success-card {
                position: relative;
                right: auto;
                bottom: auto;
                margin: 10px auto 0;
                max-width: 100%;
            }

            .status-chip {
                font-size: 12px;
                padding: 7px 10px;
            }
        }
    </style>
</head>

<body>
    <div class="facelink-shell">
        <div class="facelink-card">
            <button type="button" class="fullscreen-btn" id="fs-toggle" title="Toggle Fullscreen">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
            </button>
            <div class="facelink-header">
                <div class="badge">Face punch</div>
                <h3>
                    <?php
                    if (!empty($link->is_global)) {
                        echo html_escape($link->name ?: 'All Staff');
                    } else {
                        echo html_escape(trim(($staff['firstname'] ?? '') . ' ' . ($staff['lastname'] ?? '')));
                    }
                    ?>
                </h3>
                <p>Keep your face inside the frame. We auto-detect and punch you in/out.</p>
            </div>
            <div class="video-wrap">
                <video id="facelink-webcam" autoplay muted playsinline></video>
                <div class="status-chip" id="facelink-status">Initializing...</div>
            </div>
            <div class="success-card hidden" id="facelink-success">
                <h4 id="facelink-success-action">Checked in</h4>
                <p class="meta" id="facelink-success-name"></p>
                <p class="meta" id="facelink-success-role"></p>
                <p class="meta" id="facelink-success-time"></p>
            </div>
        </div>
    </div>
    <script>
        window.faceLinkVerifyUrl = '<?php echo site_url('facelogin/facelink/verify'); ?>';
        window.faceLinkToken = '<?php echo html_escape($link->token); ?>';
        window.faceLinkModelPath = '<?php echo rtrim(module_dir_url('facelogin', 'assets/models'), '/'); ?>';
        window.csrfData = {
            token_name: '<?php echo $this->security->get_csrf_token_name(); ?>',
            hash: '<?php echo $this->security->get_csrf_hash(); ?>'
        };
    </script>
    <script src="https://unpkg.com/webcam-easy/dist/webcam-easy.min.js"></script>
    <script src="<?php echo module_dir_url('facelogin', 'assets/js/face-api.min.js'); ?>"></script>
    <script src="<?php echo module_dir_url('facelogin', 'assets/js/face-public-link.js'); ?>"></script>
    <script>
        document.getElementById('fs-toggle').addEventListener('click', function () {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error(`Error attempting to enable fullscreen: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });
    </script>
</body>

</html>