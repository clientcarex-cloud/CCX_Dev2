(function($) {
    "use strict";

    var idleTime = 0;
    var activeTime = 0;
    var lastActivity = new Date().getTime();
    var idleThreshold = 60000; // 1 minute to be considered idle
    var heartbeatInterval = 60000; // Send data every minute
    var isIdle = false;

    // Events to detect activity
    var events = ['mousemove', 'keydown', 'click', 'scroll'];

    function resetTimer() {
        lastActivity = new Date().getTime();
        if (isIdle) {
            isIdle = false;
            // console.log("User is back active");
        }
    }

    events.forEach(function(e) {
        $(document).on(e, resetTimer);
    });

    // Check activity every second
    setInterval(function() {
        var now = new Date().getTime();
        if (now - lastActivity > idleThreshold) {
            isIdle = true;
            idleTime++;
        } else {
            activeTime++;
        }
    }, 1000);

    // Send heartbeat
    setInterval(function() {
        if (activeTime === 0 && idleTime === 0) return;

        var data = {
            active_seconds: activeTime,
            idle_seconds: idleTime,
            csrf_token_name: csrfData.hash // Assuming global csrfData exists in Perfex
        };
        
        // Add CSRF token manually if csrfData is not available or handled by jquery ajax setup
        if (typeof csrfData !== 'undefined') {
            data[csrfData.token_name] = csrfData.hash;
        }

        $.post(admin_url + 'facelogin/heartbeat', data, function(response) {
            // Reset counters after successful send
            activeTime = 0;
            idleTime = 0;
        });

    }, heartbeatInterval);

})(jQuery);
