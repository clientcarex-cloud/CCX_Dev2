<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Token Display - Now Serving</title>
    <link href="<?php echo base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            overflow: hidden;
            /* Hide scrollbars for TV feel */
            color: #333;
        }

        .container-fluid {
            padding: 0;
            height: 100vh;
        }

        .left-panel {
            background-color: #ffffff;
            height: 100vh;
            border-right: 1px solid #e3e3e3;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .right-panel {
            background-color: #f8fafc;
            height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .waiting-container {
            flex: 1;
            overflow-y: auto;
        }

        .ad-container {
            height: 40%;
            /* Adjust based on preference, maybe 50% */
            margin-top: 20px;
            border-top: 2px solid #ddd;
            padding-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: black;
        }

        .ad-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .ad-container iframe {
            width: 100%;
            height: 100%;
        }

        .serving-title {
            font-size: 3em;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        .serving-token {
            font-size: 10em;
            font-weight: 700;
            color: #2563eb;
            line-height: 1;
        }

        .serving-name {
            font-size: 3em;
            margin-top: 20px;
            color: #333;
        }

        .waiting-title {
            font-size: 2em;
            color: #555;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .waiting-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .waiting-item {
            background: #fff;
            margin-bottom: 15px;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.8em;
        }

        .waiting-token {
            font-weight: bold;
            color: #0d9488;
        }

        .blink {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0.5;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7 left-panel" id="now-serving-container">
                <div class="serving-title">Now Serving</div>
                <div class="serving-token" id="current-token">-</div>
                <div class="serving-name" id="current-name">Please Wait...</div>
            </div>
            <div class="col-md-5 right-panel">
            <div class="waiting-title"><?php echo _l('up_next'); ?></div> <!-- Localized -->
            <div class="waiting-container">
                <ul class="waiting-list" id="waiting-list">
                    <!-- Waiting items will be injected here -->
                </ul>
            </div>
            
            <?php if(isset($display) && $display['ad_type'] != 'none') { ?>
                <div class="ad-container">
                    <?php if($display['ad_type'] == 'image') { ?>
                        <img src="<?php echo $display['ad_url']; ?>" alt="Ad">
                    <?php } elseif($display['ad_type'] == 'youtube') { ?>
                        <iframe src="https://www.youtube.com/embed/<?php echo $display['ad_url']; ?>?autoplay=1&mute=1&loop=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        </div>
    </div>

    <script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script>
        const POLLING_INTERVAL = 3000; // 3 seconds
        const API_URL = "<?php echo site_url('token_system/token_display/get_queue_data'); ?>";

        let lastTokenId = null;

        function fetchQueue() {
            $.getJSON(API_URL, function (data) {
                // Update Now Serving
                if (data.serving.length > 0) {
                    const current = data.serving[0]; // Take the first one if multiple (rare)
                    $('#current-token').text(current.token_number);
                    $('#current-name').text(current.patient_name);

                    // Play sound if token changed
                    if (lastTokenId !== current.id) {
                        playNotificationSound();
                        lastTokenId = current.id;
                        // Add blink effect temporarily
                        $('#current-token').addClass('blink');
                        setTimeout(() => $('#current-token').removeClass('blink'), 5000);
                    }
                } else {
                    $('#current-token').text("-");
                    $('#current-name').text("Counter Closed / Waiting");
                    lastTokenId = null;
                }

                // Update Waiting List
                const waitingList = $('#waiting-list');
                waitingList.empty();
                if (data.waiting.length > 0) {
                    data.waiting.forEach(function (token) {
                        const li = `<li class="waiting-item">
                        <span class="waiting-token">${token.token_number}</span>
                        <span>${token.patient_name}</span>
                    </li>`;
                        waitingList.append(li);
                    });
                } else {
                    waitingList.append('<li class="waiting-item" style="justify-content:center; color:#999;">Queue Empty</li>');
                }
            });
        }

        function playNotificationSound() {
            // Simple beep or chime using Audio API ? 
            // Or a hosted file. For now, let's try a strict browser beep logic if possible, 
            // or just leave a placeholder for the user to add their own mp3.
            // Usually requires user interaction first to play audio. 
            // We will assume this page is 'interacted' with once (clicked) to fullscreen it.

            // Uncomment below to check functionality if an asset exists
            // var audio = new Audio('<?php echo base_url("modules/token_system/assets/notification.mp3"); ?>');
            // audio.play().catch(e => console.log("Audio play failed (interaction needed?):", e));
            console.log("DING DONG!");
        }

        // Initial fetch
        fetchQueue();
        // Poll
        setInterval(fetchQueue, POLLING_INTERVAL);

    </script>
</body>

</html>