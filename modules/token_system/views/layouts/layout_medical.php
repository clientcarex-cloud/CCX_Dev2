<!-- Medical Layout: Clean, Clinical, Trustworthy -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&display=swap');

    body {
        align-items: center;
        padding: 0 40px;
        font-size: 2em;
        font-weight: bold;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .container-fluid {
        height: 90vh;
        display: flex;
        padding: 40px;
        gap: 40px;
    }

    .card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        padding: 30px;
        display: flex;
        flex-direction: column;
    }

    .serving-card {
        flex: 2;
        align-items: center;
        justify-content: center;
        text-align: center;
        border: 4px solid #0ea5e9;
    }

    .waiting-card {
        flex: 1;
    }

    .serving-token {
        font-size: 12em;
        color: #0284c7;
        font-weight: 800;
    }

    .serving-name {
        font-size: 3em;
        color: #334155;
        margin-top: 20px;
        text-transform: capitalize;
    }

    .waiting-title {
        font-size: 1.8em;
        color: #0284c7;
        margin-bottom: 20px;
        font-weight: 600;
        border-bottom: 2px solid #e0f2fe;
        padding-bottom: 10px;
    }

    .waiting-item {
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 1.4em;
    }

    .waiting-item:last-child {
        border-bottom: none;
    }

    .waiting-token {
        font-weight: bold;
        color: #0ea5e9;
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">

<div class="header">
    <i class="fa fa-hospital-o" style="margin-right: 15px;"></i> Patient Queue
</div>

<div class="container-fluid">
    <div class="card serving-card">
        <div style="font-size: 2em; color: #64748b; letter-spacing: 1px;">Now Serving</div>
        <div class="serving-token" id="current-token">-</div>
        <div class="serving-name" id="current-name">Please wait for your number</div>

        <?php if (isset($display) && $display['ad_type'] != 'none') { ?>
            <div class="ad-container"
                style="margin-top:20px; width:80%; height:200px; background:#f0f9ff; display:flex; justify-content:center; align-items:center; border:1px solid #bae6fd; border-radius:10px; overflow:hidden;">
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

    <div class="card waiting-card">
        <div class="waiting-title">Waiting List</div>
        <div style="overflow-y: auto; flex:1;">
            <ul class="waiting-list" id="waiting-list" style="list-style: none; padding: 0; margin: 0;"></ul>
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
                    $('#current-token').css('color', '#0ea5e9'); // Light blue flash
                    setTimeout(() => $('#current-token').css('color', '#0284c7'), 1000);
                }
            } else {
                $('#current-token').text("-");
                $('#current-name').text("Please wait for your number");
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