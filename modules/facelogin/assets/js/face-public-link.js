const linkModelPath = window.faceLinkModelPath || '../modules/facelogin/assets/models';
const linkVerifyUrl = window.faceLinkVerifyUrl || '';
const linkToken = window.faceLinkToken || '';
const statusEl = document.getElementById('facelink-status');
const successEl = document.getElementById('facelink-success');
const successNameEl = document.getElementById('facelink-success-name');
const successRoleEl = document.getElementById('facelink-success-role');
const successTimeEl = document.getElementById('facelink-success-time');
const successActionEl = document.getElementById('facelink-success-action');
const webcamEl = document.getElementById('facelink-webcam');
const webcamObj = new Webcam(webcamEl, 'user');

let modelsLoaded = false;
let checking = false;
let cooldown = false;

async function initFaceLink() {
    try {
        updateStatus('Starting camera...');
        await webcamObj.start();
        updateStatus('Loading face model...');
        await loadLinkModels();
        updateStatus('Align your face in the frame');
        startLoop();
    } catch (err) {
        console.error(err);
        updateStatus('Unable to start camera. Please allow access.');
    }
}

async function loadLinkModels() {
    if (modelsLoaded) return;
    await Promise.all([
        faceapi.nets.tinyFaceDetector.load(linkModelPath),
        faceapi.nets.faceLandmark68TinyNet.load(linkModelPath),
        faceapi.nets.faceRecognitionNet.load(linkModelPath)
    ]);
    modelsLoaded = true;
}

function startLoop() {
    setInterval(async () => {
        if (checking || cooldown) {
            return;
        }
        checking = true;
        try {
            const detection = await faceapi
                .detectSingleFace(webcamEl, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks(true)
                .withFaceDescriptor();

            if (!detection) {
                updateStatus('Looking for face...');
                checking = false;
                return;
            }

            updateStatus('Verifying...');
            await verifyDescriptor(Array.from(detection.descriptor));
        } catch (err) {
            console.error(err);
            updateStatus('Detection error, retrying...');
        } finally {
            checking = false;
        }
    }, 1500);
}

async function verifyDescriptor(descriptor) {
    const fd = new FormData();
    fd.append('token', linkToken);
    fd.append('user_json', JSON.stringify(descriptor));
    if (window.csrfData && csrfData.token_name && csrfData.hash) {
        fd.append(csrfData.token_name, csrfData.hash);
    }

    try {
        const res = await fetch(linkVerifyUrl, {
            method: 'POST',
            body: fd,
        });
        const data = await res.json();
        if (data.status === 'success') {
            showSuccess(data);
        } else {
            updateStatus(data.message || 'Not matched, hold still...');
        }
    } catch (err) {
        console.error(err);
        updateStatus('Server error, retrying...');
    }
}

function updateStatus(text) {
    if (statusEl) {
        statusEl.innerText = text;
    }
}

function showSuccess(data) {
    cooldown = true;
    if (successEl) {
        successActionEl.innerText = data.action === 'checked_out' ? 'Checked out' : 'Checked in';
        successNameEl.innerText = data.staff_name || '';
        successRoleEl.innerText = data.role || '';
        successTimeEl.innerText = data.time || '';
        successEl.classList.remove('hidden');
    }
    updateStatus('Done');
    setTimeout(() => {
        if (successEl) {
            successEl.classList.add('hidden');
        }
        cooldown = false;
        updateStatus('Align your face in the frame');
    }, 4000);
}

document.addEventListener('DOMContentLoaded', initFaceLink);
