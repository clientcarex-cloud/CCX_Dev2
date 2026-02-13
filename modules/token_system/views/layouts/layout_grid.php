<!-- Grid Layout: Card Based -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap');

    body {
        font-family: 'Nunito', sans-serif;
        background: #eef2f5;
        margin: 0;
        padding: 20px;
        height: 100vh;
        padding: 20px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        grid-template-rows: 1fr 2fr;
        gap: 20px;
    }

    .main-display {
        grid-column: 1 / 4;
        grid-row: 1 / 2;
        background: #2563eb;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: space-around;
        padding: 20px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
    }

    .current-token {
        font-size: 8em;
        font-weight: bold;
    }

    .current-name {
        font-size: 4em;
    }

    .waiting-grid {
        grid-column: 1 / 4;
        grid-row: 2 / 3;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        align-content: start;
        overflow-y: auto;
    }

    .grid-item {
        background: #444;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        font-size: 2em;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        border-left: 5px solid #0ea5e9;
    }

    .grid-token {
        font-weight: bold;
        color: #0ea5e9;
        display: block;
        margin-bottom: 10px;
    }

    .grid-name {
        font-size: 0.6em;
        color: #ccc;
    }

    /* Ad float */
    .ad-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 300px;
        height: 180px;
        background: black;
        z-index: 100;
        border: 2px solid white;
    }
</style>

<div class="container">
    <div class="main-display">
        <div>
            <div style="font-size: 1.5em; opacity: 0.8; text-transform: uppercase;">Now Serving</div>
            <div class="current-token" id="current-token">-</div>
        </div>
        <div class="current-name" id="current-name">Waiting...</div>
    </div>

    <div class="waiting-grid" id="waiting-grid">
        <!-- Grid Items Here -->
    </div>
</div>

<?php if (isset($display) && $display['ad_type'] != 'none') { ?>
    <div class="ad-float">
        <?php if ($display['ad_type'] == 'image') { ?>
            <img src="<?php echo $display['ad_url']; ?>" style="max-height:100%; max-width:100%; display:block; margin:auto;">
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
                    $('.main-display').css('background', '#1d4ed8');
                    setTimeout(() => $('.main-display').css('background', '#2563eb'), 1000);
                }
            } else {
                $('#current-token').text("-");
                $('#current-name').text("Waiting...");
                lastTokenId = null;
            }

            const grid = $('#waiting-grid');
            grid.empty();
            if (data.waiting.length > 0) {
                data.waiting.forEach(function (token) {
                    const item = `<div class="grid-item">
                        <span class="grid-token">${token.token_number}</span>
                        <span class="grid-name">${token.patient_name}</span>
                    </div>`;
                    grid.append(item);
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