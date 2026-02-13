const modelPath = '../modules/facelogin/assets/models';
const webcamElement = document.getElementById('webcam');
const webcam = new Webcam(webcamElement, 'user');
const canvas = document.getElementById('face-canvas');
const ctx = canvas.getContext('2d');
const verifyUrl = window.faceVerifyUrl || 'facelogin/faceid';


let faceVerificationInterval;
let modelsLoaded = false;

function showFaceSuccessModal() {
    const modal = document.getElementById("face-id-modal");
    if (modal) {
        modal.style.display = "flex";

        webcam.start().then(async () => {
            updateFaceStatus("Looking for face...", "#fff");
            drawFaceGuideOverlay();
            await loadFaceModels();
            startFaceVerificationLoop();

        }).catch(err => {
            console.error("Webcam error:", err);
            updateFaceStatus("Camera access denied", "#ff4d4d");

        });
    }
}

function hideFaceModal() {
    const modal = document.getElementById("face-id-modal");
    if (modal) {
        modal.style.display = "none";

    }
    webcam.stop();
    clearInterval(faceVerificationInterval);
}

async function loadFaceModels() {
    if (modelsLoaded) return;

    await Promise.all([
        faceapi.nets.tinyFaceDetector.load(modelPath),
        faceapi.nets.faceLandmark68TinyNet.load(modelPath),
        faceapi.nets.faceRecognitionNet.load(modelPath)
    ]);

    modelsLoaded = true;
}

function startFaceVerificationLoop(userId = 1) {
    faceVerificationInterval = setInterval(async () => {
        const detection = await faceapi
            .detectSingleFace(webcamElement, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks(true)
            .withFaceDescriptor();

        if (!detection) {
            updateFaceStatus("Face not found", "#ffcc00");
            return;
        }
        updateFaceStatus("Verifying...", "#00ccff");

        const descriptor = Array.from(detection.descriptor);

        $.post(verifyUrl, {
            user_id: userId,
            csrf_token_name: $('[name=csrf_token_name]').val(),
            user_json: JSON.stringify(descriptor)
        }, function(response) {
            if (response.status === "success") {
                updateFaceStatus("Success", "#00ffaa");
                clearInterval(faceVerificationInterval);
                document.querySelector('.face-id-status').innerText = 'Success';
                document.querySelector('.face-id-status').style.color = '#00ffaa';
                hideFaceModal();
                setTimeout(() => {
                    location.reload();

                }, 1000);

            } else {
                updateFaceStatus("Login failed", "#ff4d4d");
                console.log("Face not matched, retrying...");
            }
        }, 'json').fail(function() {
            updateFaceStatus("Server error during face verification.", "#ff4d4d");
            console.log("Server error during face verification., retrying...");
        });
    }, 1500); // Retry every 1.5s
}

function updateFaceStatus(text, color = '#00ffaa') {
    const statusEl = document.querySelector('.face-id-status');
    if (statusEl) {
        statusEl.innerText = text;
        statusEl.style.color = color;
    }
}

function drawFaceGuideOverlay() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const faceWidth = 80; // Adjusted for smaller video
    const faceHeight = 100;

    // Face oval
    ctx.beginPath();
    ctx.ellipse(centerX, centerY, faceWidth / 2, faceHeight / 2, 0, 0, 2 * Math.PI);
    ctx.strokeStyle = 'rgba(0, 255, 0, 0.8)';
    ctx.lineWidth = 2;
    ctx.stroke();

    // Eyes reference
    ctx.beginPath();
    ctx.arc(centerX - 15, centerY - 12, 3, 0, 2 * Math.PI); // Left eye
    ctx.arc(centerX + 15, centerY - 12, 3, 0, 2 * Math.PI); // Right eye
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.5)';
    ctx.stroke();

    // Nose tip
    ctx.beginPath();
    ctx.arc(centerX, centerY + 8, 2.5, 0, 2 * Math.PI);
    ctx.stroke();

    // Text
    ctx.font = "10px Arial";
    ctx.fillStyle = 'rgba(255, 255, 255, 0.7)';
    ctx.textAlign = "center";
    ctx.fillText("Align your face inside", centerX, centerY + faceHeight / 2 + 12);
}
