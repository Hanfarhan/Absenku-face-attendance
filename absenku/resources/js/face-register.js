import { FaceMesh } from '@mediapipe/face_mesh';
import { Camera } from '@mediapipe/camera_utils';

// ============================================================
// STATE
// ============================================================
let faceMesh       = null;
let camera         = null;
let faRegActive    = false;
let faRegCaptures  = [];
let faRegCapturing = false;
let faceDetected   = false;
let lastFaceState  = false;
let currentYaw     = 0;
let currentPitch   = 0;

const FA_REG_TOTAL = 10;

// Threshold dilonggarkan agar lebih natural & mudah dicapai
const FA_REG_STEPS = [
    { label: 'Lihat lurus ke kamera',       arrow: null, yawMin: -20, yawMax: 20,  pitchMin: -25, pitchMax: 25  },
    { label: 'Lihat lurus ke kamera',       arrow: null, yawMin: -20, yawMax: 20,  pitchMin: -25, pitchMax: 25  },
    { label: 'Geser sedikit ke kiri',       arrow: '←',  yawMin: -50, yawMax: -5,  pitchMin: -30, pitchMax: 30  },
    { label: 'Geser sedikit ke kiri',       arrow: '←',  yawMin: -50, yawMax: -5,  pitchMin: -30, pitchMax: 30  },
    { label: 'Geser sedikit ke kanan',      arrow: '→',  yawMin: 5,   yawMax: 50,  pitchMin: -30, pitchMax: 30  },
    { label: 'Geser sedikit ke kanan',      arrow: '→',  yawMin: 5,   yawMax: 50,  pitchMin: -30, pitchMax: 30  },
    { label: 'Sedikit menundukkan kepala',  arrow: '↓',  yawMin: -25, yawMax: 25,  pitchMin: 5,   pitchMax: 50  },
    { label: 'Sedikit menundukkan kepala',  arrow: '↓',  yawMin: -25, yawMax: 25,  pitchMin: 5,   pitchMax: 50  },
    { label: 'Sedikit mendongakkan kepala', arrow: '↑',  yawMin: -25, yawMax: 25,  pitchMin: -50, pitchMax: -5  },
    { label: 'Sedikit mendongakkan kepala', arrow: '↑',  yawMin: -25, yawMax: 25,  pitchMin: -50, pitchMax: -5  },
];

// ============================================================
// SHOW / HIDE CAMERA SECTION
// ============================================================
function faShowRegCamera() {
    const section = document.getElementById('fa-reg-camera-section');
    section.style.display = 'block';
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function faHideRegCamera() {
    faRegStopCamera();
    document.getElementById('fa-reg-camera-section').style.display = 'none';
}

// ============================================================
// START CAMERA + MEDIAPIPE
// ============================================================
async function faRegStartCamera() {
    if (faRegActive) { faRegStopCamera(); return; }

    faRegSetStatus('Memuat MediaPipe...', 'amber');

    const video = document.getElementById('fa-reg-video');

    faceMesh = new FaceMesh({
        locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`
    });

    faceMesh.setOptions({
        maxNumFaces: 1,
        refineLandmarks: false,
        minDetectionConfidence: 0.6,
        minTrackingConfidence: 0.6,
    });

    faceMesh.onResults(faRegOnResults);

    camera = new Camera(video, {
        onFrame: async () => {
            if (faceMesh && faRegActive) {
                await faceMesh.send({ image: video });
            }
        },
        width: 640,
        height: 480,
        facingMode: 'user',
    });

    try {
        await camera.start();
        faRegActive = true;

        video.style.display = 'block';
        document.getElementById('fa-reg-placeholder').style.display = 'none';
        document.getElementById('fa-reg-face-box').style.display    = 'block';
        document.getElementById('fa-reg-live-badge').style.display  = 'inline-flex';

        document.getElementById('fa-reg-btn-start').innerHTML =
            `<svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
             </svg> Hentikan`;

        faRegSetStatus('Posisikan wajah di dalam kotak oval', 'blue');
        faRegShowArrow(null);

    } catch (e) {
        faRegSetStatus('Gagal mengakses kamera — periksa izin browser', 'red');
    }
}

// ============================================================
// MEDIAPIPE RESULTS — hitung yaw & pitch
// ============================================================
function faRegOnResults(results) {
    const detected = results.multiFaceLandmarks && results.multiFaceLandmarks.length > 0;

    if (detected) {
        const lm = results.multiFaceLandmarks[0];

        const noseTip  = lm[1];
        const leftEar  = lm[234];
        const rightEar = lm[454];
        const chin     = lm[152];
        const forehead = lm[10];

        const faceWidth  = Math.abs(leftEar.x - rightEar.x);
        const noseOffset = noseTip.x - (leftEar.x + rightEar.x) / 2;
        currentYaw       = (noseOffset / faceWidth) * 90;

        const faceHeight  = Math.abs(forehead.y - chin.y);
        const noseVOffset = noseTip.y - (forehead.y + chin.y) / 2;
        currentPitch      = (noseVOffset / faceHeight) * 90;

        window._lastFaceLandmarks = lm;

        // DEBUG: tampilkan nilai yaw/pitch realtime di layar
        const debugEl = document.getElementById('fa-reg-debug');
        if (debugEl) {
            debugEl.textContent = `yaw: ${currentYaw.toFixed(1)}°  pitch: ${currentPitch.toFixed(1)}°`;
        }
    } else {
        window._lastFaceLandmarks = null;
    }

    if (detected !== lastFaceState) {
        lastFaceState = detected;
        faceDetected  = detected;

        if (detected && !faRegCapturing && faRegCaptures.length === 0) {
            faRegSetStatus('Wajah terdeteksi — Bersiap untuk capture...', 'green');
            document.getElementById('fa-reg-guide-text').textContent = 'Wajah terdeteksi!';
            faRegUpdateFaceBox('detected');
            faRegStartCountdown();
        } else if (!detected && !faRegCapturing) {
            faRegSetStatus('Posisikan wajah di dalam kotak oval', 'blue');
            document.getElementById('fa-reg-guide-text').textContent = 'Posisikan wajah di dalam kotak';
            faRegUpdateFaceBox('none');
            faRegShowArrow(null);
        }
    }
}

// ============================================================
// VALIDASI ARAH
// ============================================================
function faRegCheckDirection(stepIndex) {
    const step = FA_REG_STEPS[stepIndex];
    const yawOk   = currentYaw   >= step.yawMin   && currentYaw   <= step.yawMax;
    const pitchOk = currentPitch >= step.pitchMin && currentPitch <= step.pitchMax;
    return yawOk && pitchOk;
}

// ============================================================
// UPDATE FACE BOX VISUAL
// ============================================================
function faRegUpdateFaceBox(state) {
    const corners = document.querySelectorAll('.fa-reg-corner');
    const oval    = document.getElementById('fa-reg-oval');
    const scan    = document.getElementById('fa-reg-scan-line');

    corners.forEach(c => {
        c.classList.remove('detected', 'wrong');
        if (state === 'detected' || state === 'correct') c.classList.add('detected');
        if (state === 'wrong') c.classList.add('wrong');
    });

    if (oval) {
        oval.classList.remove('detected', 'wrong');
        if (state === 'detected' || state === 'correct') oval.classList.add('detected');
        if (state === 'wrong') oval.classList.add('wrong');
    }

    if (scan) scan.style.display = (state === 'detected' || state === 'correct') ? 'block' : 'none';
}

// ============================================================
// ARROW OVERLAY
// ============================================================
function faRegShowArrow(arrow) {
    const el = document.getElementById('fa-reg-arrow-overlay');
    if (!el) return;
    if (!arrow) {
        el.style.display = 'none';
        return;
    }
    el.textContent   = arrow;
    el.style.display = 'flex';
}

// ============================================================
// COUNTDOWN (tanpa batas waktu, hanya batal jika wajah hilang)
// ============================================================
function faRegStartCountdown() {
    if (!faRegActive || faRegCapturing) return;

    let count       = 3;
    const countdown = document.getElementById('fa-reg-countdown');
    const countNum  = document.getElementById('fa-reg-countdown-num');
    countdown.style.display = 'flex';
    countNum.textContent    = count;

    const interval = setInterval(() => {
        if (!faceDetected) {
            clearInterval(interval);
            countdown.style.display = 'none';
            faRegSetStatus('Wajah hilang — posisikan ulang wajah Anda', 'amber');
            document.getElementById('fa-reg-guide-text').textContent = 'Posisikan wajah di dalam kotak';
            faRegUpdateFaceBox('none');
            return;
        }

        count--;
        if (count > 0) {
            countNum.textContent = count;
        } else {
            clearInterval(interval);
            countdown.style.display = 'none';
            faRegStartCapture();
        }
    }, 1000);
}

// ============================================================
// AUTO CAPTURE — TANPA TIMEOUT, tunggu sampai posisi benar
// ============================================================
async function faRegStartCapture() {
    if (!faRegActive || faRegCapturing) return;

    faRegCapturing = true;
    faRegCaptures  = [];

    document.getElementById('fa-reg-progress-wrap').style.display = 'block';

    for (let i = 0; i < FA_REG_TOTAL; i++) {
        if (!faRegActive) break;

        const step = FA_REG_STEPS[i];

        document.getElementById('fa-reg-direction').textContent  = step.label;
        document.getElementById('fa-reg-guide-text').textContent = step.label;
        faRegShowArrow(step.arrow);
        faRegSetStatus(`Foto ${i + 1} dari ${FA_REG_TOTAL} — ${step.label}`, 'blue');

        // Tunggu TANPA BATAS WAKTU sampai posisi benar (atau dibatalkan via stop kamera)
        const directionOk = await faRegWaitForDirection(i);

        if (!faRegActive) break;
        if (!directionOk) {
            // Hanya terjadi jika kamera dihentikan di tengah proses
            faRegCapturing = false;
            return;
        }

        faRegUpdateFaceBox('correct');
        faRegSetStatus(`Posisi tepat! Mengambil foto ${i + 1}...`, 'green');
        await faRegDelay(300);

        const dataUrl = faRegCaptureFrame();
        if (dataUrl) {
            faRegCaptures.push(dataUrl);
            faRegShowThumb(i, dataUrl);
        }

        faRegFlash();

        const pct = Math.round(((i + 1) / FA_REG_TOTAL) * 100);
        document.getElementById('fa-reg-progress-fill').style.width  = pct + '%';
        document.getElementById('fa-reg-progress-pct').textContent   = pct + '%';
        document.getElementById('fa-reg-progress-label').textContent = `Mengambil foto ${i + 1} dari ${FA_REG_TOTAL}`;
        document.getElementById('fa-reg-count-badge').textContent    = `${i + 1} / ${FA_REG_TOTAL}`;

        await faRegDelay(600);
    }

    faRegCapturing = false;
    faRegShowArrow(null);

    if (faRegCaptures.length === FA_REG_TOTAL) {
        faRegUpdateFaceBox('none');
        faRegSetStatus('Semua foto berhasil diambil! Tekan "Simpan Data Wajah"', 'green');
        document.getElementById('fa-reg-guide-text').textContent    = '✓ Pengambilan foto selesai!';
        document.getElementById('fa-reg-submit-wrap').style.display = 'block';
    } else {
        faRegSetStatus('Pengambilan foto tidak lengkap — coba lagi', 'red');
        faRegUpdateFaceBox('none');
    }
}

// ============================================================
// TUNGGU ARAH BENAR — TANPA TIMEOUT
// Hanya berhenti jika: (1) arah sudah benar, atau (2) kamera dimatikan
// ============================================================
function faRegWaitForDirection(stepIndex) {
    return new Promise(resolve => {
        let lastWrong = false;

        const interval = setInterval(() => {
            if (!faRegActive) {
                clearInterval(interval);
                resolve(false);
                return;
            }

            if (!faceDetected) {
                faRegUpdateFaceBox('none');
                lastWrong = false;
                return;
            }

            const correct = faRegCheckDirection(stepIndex);

            if (correct) {
                clearInterval(interval);
                resolve(true);
            } else if (!lastWrong) {
                faRegUpdateFaceBox('wrong');
                lastWrong = true;
            }
        }, 150);
    });
}

// ============================================================
// HELPERS
// ============================================================
function faRegCaptureFrame() {
    const video  = document.getElementById('fa-reg-video');
    const canvas = document.createElement('canvas');
    canvas.width  = video.videoWidth  || 640;
    canvas.height = video.videoHeight || 480;
    canvas.getContext('2d').drawImage(video, 0, 0);
    return canvas.toDataURL('image/jpeg', 0.90);
}

function faRegShowThumb(index, dataUrl) {
    const slot = document.getElementById(`fa-reg-thumb-${index}`);
    if (!slot) return;
    const img = document.createElement('img');
    img.src   = dataUrl;
    img.alt   = `Foto ${index + 1}`;
    slot.innerHTML = '';
    slot.appendChild(img);
}

function faRegFlash() {
    const flash = document.getElementById('fa-reg-flash');
    flash.classList.add('active');
    setTimeout(() => flash.classList.remove('active'), 150);
}

function faRegDelay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function faRegSetStatus(msg, type) {
    const bar = document.getElementById('fa-reg-status-bar');
    if (!bar) return;
    const dot  = bar.querySelector('.fa-status-dot');
    const text = document.getElementById('fa-reg-status-text');

    const map = {
        blue:  { bg: 'var(--blue-50)',  dot: 'var(--blue-600)',  text: 'var(--blue-800)'  },
        green: { bg: 'var(--green-50)', dot: 'var(--green-600)', text: 'var(--green-800)' },
        amber: { bg: 'var(--amber-50)', dot: 'var(--amber-600)', text: 'var(--amber-600)' },
        red:   { bg: 'var(--red-50)',   dot: '#E24B4A',          text: 'var(--red-600)'   },
    };

    const s          = map[type] || map.blue;
    bar.style.background = s.bg;
    bar.style.color      = s.text;
    dot.style.background = s.dot;
    text.textContent     = msg;
}

// ============================================================
// STOP CAMERA
// ============================================================
function faRegStopCamera() {
    if (camera)   { camera.stop();    camera   = null; }
    if (faceMesh) { faceMesh.close(); faceMesh = null; }

    faRegActive    = false;
    faRegCapturing = false;
    faceDetected   = false;
    lastFaceState  = false;
    faRegCaptures  = [];
    currentYaw     = 0;
    currentPitch   = 0;

    const video = document.getElementById('fa-reg-video');
    if (video) {
        video.style.display = 'none';
        video.srcObject     = null;
    }

    const els = {
        'fa-reg-placeholder':   'flex',
        'fa-reg-face-box':      'none',
        'fa-reg-scan-line':     'none',
        'fa-reg-live-badge':    'none',
        'fa-reg-progress-wrap': 'none',
        'fa-reg-submit-wrap':   'none',
        'fa-reg-countdown':     'none',
    };
    Object.entries(els).forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) el.style.display = val;
    });

    faRegShowArrow(null);
    faRegUpdateFaceBox('none');

    document.getElementById('fa-reg-btn-start').innerHTML =
        `<svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
            <path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2S13.77 8.8 12 8.8 8.8 10.23 8.8 12s1.43 3.2 3.2 3.2zm9-8.2h-2.83l-1.42-1.6H8.25L6.83 7H4C2.9 7 2 7.9 2 9v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2z"/>
         </svg> Mulai Kamera`;

    for (let i = 0; i < FA_REG_TOTAL; i++) {
        const slot = document.getElementById(`fa-reg-thumb-${i}`);
        if (slot) slot.innerHTML = `<div class="fa-reg-thumb-empty">${i + 1}</div>`;
    }

    const badge = document.getElementById('fa-reg-count-badge');
    if (badge) badge.textContent = `0 / ${FA_REG_TOTAL}`;

    faRegSetStatus('Tekan "Mulai Kamera" untuk memulai pendaftaran', 'blue');
}

// ============================================================
// SUBMIT KE LARAVEL
// ============================================================
async function faRegSubmit() {
    if (faRegCaptures.length !== FA_REG_TOTAL) return;

    faRegSetStatus('Menyimpan data wajah... Mohon tunggu', 'amber');
    document.getElementById('fa-reg-submit-wrap').querySelector('button').disabled = true;

    try {
        const response = await fetch('/face-register/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ captures: faRegCaptures }),
        });

        const data = await response.json();

        if (data.success) {
            faRegSetStatus('Wajah berhasil didaftarkan!', 'green');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            faRegSetStatus('Gagal — ' + (data.message ?? 'Coba lagi'), 'red');
            document.getElementById('fa-reg-submit-wrap').querySelector('button').disabled = false;
        }
    } catch (e) {
        faRegSetStatus('Terjadi kesalahan koneksi, coba lagi', 'red');
        document.getElementById('fa-reg-submit-wrap').querySelector('button').disabled = false;
    }
}

// ============================================================
// EXPOSE KE GLOBAL
// ============================================================
window.faShowRegCamera  = faShowRegCamera;
window.faHideRegCamera  = faHideRegCamera;
window.faRegStartCamera = faRegStartCamera;
window.faRegSubmit      = faRegSubmit;