<!-- Focus Layout: Maximum Visibility -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;900&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .top-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #000;
    }

    .bottom-panel {
        height: 15vh;
        background: #333;
        display: flex;
        align-items: center;
        overflow: hidden;
        white-space: nowrap;
    }

    .serving-token {
        font-size: 30em;
        font-weight: 800;
        color: #4ade80;
        line-height: 0.8;
        margin-top: -50px;
        text-shadow: 0 0 20px rgba(74, 222, 128, 0.5);
    }

    .serving-name {
        font-size: 5em;
        color: #fff;
        margin-top: 20px;
    }

    .scrolling-list {
        animation: scroll 20s linear infinite;
        display: inline-block;
        padding-left: 100%;
    }

    .scroll-item {
        display: inline-block;
        font-size: 3em;
        margin-right: 50px;
        color: #aaa;
    }

    .scroll-item b {
        color: #fff;
    }

    @keyframes scroll {
        0% {
            transform: translate(0, 0);
        }

        100% {
            transform: translate(-100%, 0);
        }
    }
</style>

<div class="container-fluid">
    <div class="top-panel">
        <div style="font-size: 3em; color: #888; text-transform: uppercase;">Now Serving</div>
        <div class="serving-token" id="current-token">-</div>
        <div class="serving-name" id="current-name">Waiting...</div>

        <?php if (isset($display) && $display['ad_type'] != 'none') { ?>
            <div class="ad-container"
                style="position:absolute; bottom:16vh; right:20px; width:300px; height:200px; background:black; border:2px solid #444;">
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
    <div class="bottom-panel">
        <div class="scrolling-list" id="waiting-marquee">
            <!-- Items injected here -->
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
                    $('#current-token').css('color', '#fff'); // Flash white
                    setTimeout(() => $('#current-token').css('color', '#4ade80'), 2000);
                }
            } else {
                $('#current-token').text("-");
                $('#current-name').text("Waiting...");
                lastTokenId = null;
            }

            const marquee = $('#waiting-marquee');
            marquee.empty();
            if (data.waiting.length > 0) {
                data.waiting.forEach(function (token) {
                    const item = `<div class="scroll-item">
                        <b>${token.token_number}</b> ${token.patient_name}
                    </div>`;
                    marquee.append(item);
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