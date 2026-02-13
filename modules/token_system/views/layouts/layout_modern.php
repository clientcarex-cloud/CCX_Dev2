<!-- Modern Layout -->
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background: #f0f2f5;
        color: #333;
        overflow: hidden;
    }

    .container-fluid {
        height: 100vh;
        display: flex;
    }

    .left-panel {
        flex: 0 0 60%;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border-right: 1px solid #ddd;
    }

    .right-panel {
        flex: 1;
        background: #fafafa;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }

    .serving-token {
        font-size: 15em;
        font-weight: 700;
        color: #2563eb;
        line-height: 1;
    }

    .serving-name {
        font-size: 4em;
        color: #444;
        margin-top: 20px;
        text-transform: uppercase;
    }

    .waiting-item {
        background: #fff;
        margin-bottom: 20px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        font-size: 2em;
        display: flex;
        justify-content: space-between;
    }

    .waiting-token {
        color: #0d9488;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <div class="left-panel">
        <div style="font-size: 2em; color: #888; text-transform: uppercase; letter-spacing: 2px;">Now Serving</div>
        <div class="serving-token" id="current-token">-</div>
        <div class="serving-name" id="current-name">Waiting...</div>
    </div>
    <div class="right-panel">
        <div style="font-size: 2em; color: #555; border-bottom: 2px solid #ddd; margin-bottom: 20px;">Up Next</div>
        <div class="waiting-container" style="flex:1; overflow:hidden;">
            <ul class="waiting-list" id="waiting-list" style="list-style:none; padding:0;"></ul>
        </div>
        <!-- Ad Container Injection Point -->
        <?php if (isset($display) && $display['ad_type'] != 'none') { ?>
            <div class="ad-container"
                style="height: 40%; background:black; display:flex; justify-content:center; align-items:center;">
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
                    $('#current-token').css('color', 'red');
                    setTimeout(() => $('#current-token').css('color', ''), 2000);
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
                        <span class="waiting-token">${token.token_number}</span>
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