<!-- Dark Futuristic Layout: Neon, High-Tech -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap');

    body {
        font-family: 'Orbitron', sans-serif;
        background: #0f172a;
        /* Slate 900 */
        color: #fff;
        margin: 0;
        padding: 0;
        height: 100vh;

        .main-card {
            flex: 1;
            background: #1e293b;
            border: 2px solid #0ea5e9;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 30px rgba(14, 165, 233, 0.3);
            margin-right: 40px;
            position: relative;
        }

        .side-card {
            width: 400px;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .serving-token {
            font-size: 18em;
            color: #fff;
            text-shadow: 0 0 10px #0ea5e9, 0 0 20px #0ea5e9;
            font-weight: bold;
        }

        .serving-name {
            font-size: 3.5em;
            color: #94a3b8;
            margin-top: 30px;
            text-transform: uppercase;
        }

        .list-header {
            font-size: 2em;
            color: #fff;
            border-bottom: 1px solid #334155;
            padding-bottom: 15px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .waiting-item {
            background: #334155;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 10px;
            font-size: 1.5em;
            color: #fff;
            display: flex;
            justify-content: space-between;
        }

        .waiting-item span:first-child {
            color: #38bdf8;
            font-weight: bold;
        }

        /* Ad Overlay if needed or integrated */
        .ad-overlay {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 40%;
            height: 30%;
            background: #000;
            border: 1px solid #334155;
            display: flex;
            justify-content: center;
            align-items: center;
        }
</style>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

<div class="container-fluid">
    <div class="main-card">
        <div
            style="font-size: 2.5em; color: #38bdf8; text-transform: uppercase; letter-spacing: 5px; margin-bottom: 20px;">
            Now Serving</div>
        <div class="serving-token" id="current-token">-</div>
        <div class="serving-name" id="current-name">Waiting...</div>

        <?php if (isset($display) && $display['ad_type'] != 'none') { ?>
            <div class="ad-overlay">
                <?php if ($display['ad_type'] == 'image') { ?>
                    <img src="<?php echo $display['ad_url']; ?>" style="max-height:100%; max-width:100%;">
                <?php } elseif ($display['ad_type'] == 'youtube') {
                    $video_id = $display['ad_url'];
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $display['ad_url'], $match)) {
                        $video_id = $match[1];
                    }
                    $mute = isset($display['yt_mute']) ? $display['yt_mute'] : 1;
                    $loop = isset($display['yt_loop']) ? $display['yt_loop'] : 1;
                    $params = "autoplay=1&controls=0&enablejsapi=1";
                    $params .= "&mute=" . $mute;
                    if ($loop == 1) {
                        $params .= "&loop=1&playlist=" . $video_id;
                    }
                    ?>
                    <iframe src="https://www.youtube.com/embed/<?php echo $video_id; ?>?<?php echo $params; ?>"
                        style="width:100%; height:100%;" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen></iframe>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <div class="side-card">
        <div class="list-header">Up Next</div>
        <div style="overflow-y:auto; flex:1;">
            <ul class="waiting-list" id="waiting-list" style="list-style:none; padding:0;"></ul>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>
<script>
    const POLLING_INTERVAL = 3000;
    const DISPLAY_ID = "<?php echo isset($display) ? $display['id'] : ''; ?>";
    const API_URL = "<?php echo site_url('token_system/token_display/get_queue_data?display_id='); ?>" + DISPLAY_ID;

    let lastTokenId = null;

    function fetchQueue() {
        $.getJSON(API_URL, function (data) {
            if (data.serving.length > 0) {
                const current = data.serving[0];
                $('#current-token').text(current.token_number);
                $('#current-name').text(current.patient_name);

                if (lastTokenId !== current.id) {
                    playNotificationSound();
                    lastTokenId = current.id;
                    // Neon flash effect
                    $('#current-token').css('text-shadow', '0 0 50px #fff');
                    setTimeout(() => $('#current-token').css('text-shadow', '0 0 10px #0ea5e9, 0 0 20px #0ea5e9'), 1000);
                }
            } else {
                $('#current-token').text("-");
                $('#current-name').text("Waiting...");
                lastTokenId = null;
            }

            const waitingList = $('#waiting-list');
            waitingList.empty();
            if (data.waiting.length > 0) {
                data.waiting.forEach(function (token) {
                    const li = `<li class="waiting-item">
                        <span>${token.token_number}</span>
                        <span>${token.patient_name}</span>
                    </li>`;
                    waitingList.append(li);
                });
            }
        });
    }

    function playNotificationSound() {
        var audio = new Audio('<?php echo base_url("modules/token_system/assets/notification.mp3"); ?>');
        audio.play().catch(e => console.log("Audio needed user interaction"));
    }

    fetchQueue();
    setInterval(fetchQueue, POLLING_INTERVAL);
</script>
</body>

</html>