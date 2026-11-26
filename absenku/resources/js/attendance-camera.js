import { FaceMesh } from '@mediapipe/face_mesh';
import { Camera } from '@mediapipe/camera_utils';

// ============================================================
// STATE
// ============================================================
let attFaceMesh = null;
let attCamera = null;
let attCamActive = false;
let attProcessing = false;
let attFaceDetected = false;
let attLastState = false;
let attStableCount = 0;

// ── Retry State ───────────────────────────────────────────
let attAttemptCount = 0;            // percobaan ke-berapa saat ini
const ATT_MAX_ATTEMPTS     = 3;     // max percobaan sebelum kamera berhenti
const ATT_STABLE_THRESHOLD = 20;   // frame stabil sebelum trigger (~0.7 detik @ 30fps)

// ── Camera Warm-up ─────────────────────────────────────────
// Kamera butuh waktu untuk auto-focus & auto-exposure sebelum capture
// Selama warm-up, wajah boleh terdeteksi tapi BELUM trigger scan
let attWarmingUp = false;
const ATT_WARMUP_MS = 2500;   // 2.5 detik warm-up setelah kamera aktif

// Ambiguous zone: skor antara batas bawah dan threshold
// Jika skor di zona ini, mungkin genuine dengan masalah lingkungan → beri guidance
const ATT_LIVENESS_THRESHOLD = 0.50;  // default, sinkron dengan config FastAPI
const ATT_AMBIGUOUS_FLOOR = 0.30;  // bawah ini = spoof, atas ini = ambiguous

// ── Research Mode State ────────────────────────────────────
let attResearchOpen = false;

// ============================================================
// START CAMERA
// ============================================================
async function faStartCamera() {
    if (attCamActive) { faStopCamera(); return; }

    faSetStatus('Memuat kamera...', 'amber');

    const video = document.getElementById('fa-cam-video');

    attFaceMesh = new FaceMesh({
        locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`
    });

    attFaceMesh.setOptions({
        maxNumFaces: 2,           // deteksi max 2 agar bisa tahu jika ada lebih dari 1
        refineLandmarks: false,
        minDetectionConfidence: 0.6,
        minTrackingConfidence: 0.6,
    });

    attFaceMesh.onResults(attOnResults);

    attCamera = new Camera(video, {
        onFrame: async () => {
            if (attFaceMesh && attCamActive) {
                await attFaceMesh.send({ image: video });
            }
        },
        width: 640,
        height: 480,
        facingMode: 'user',
    });

    try {
        await attCamera.start();
        attCamActive = true;
        attAttemptCount = 0;  // reset percobaan setiap kamera dimulai

        video.style.display = 'block';
        document.getElementById('fa-cam-placeholder').style.display = 'none';
        document.getElementById('fa-face-overlay').style.display = 'block';
        document.getElementById('fa-live-badge').style.display = 'inline-flex';

        document.getElementById('fa-btn-start').innerHTML =
            `<svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
             </svg> Hentikan Kamera`;

        const btnCheckin = document.getElementById('fa-btn-checkin');
        if (btnCheckin) btnCheckin.style.display = 'none';

        // ── Warm-up period: tunggu kamera fokus & exposure stabil ──
        attWarmingUp = true;
        faSetStatus('Kamera menyesuaikan fokus...', 'amber');

        // Countdown visual selama warm-up
        let remaining = Math.ceil(ATT_WARMUP_MS / 1000);
        const countdown = setInterval(() => {
            remaining--;
            if (remaining > 0) {
                faSetStatus(`Kamera menyesuaikan fokus... (${remaining}s)`, 'amber');
            }
        }, 1000);

        setTimeout(() => {
            clearInterval(countdown);
            attWarmingUp = false;
            if (attCamActive) {
                faSetStatus('Posisikan wajah Anda di dalam kotak', 'blue');
            }
        }, ATT_WARMUP_MS);

        attUpdateAttemptIndicator();

    } catch (e) {
        faSetStatus('Gagal mengakses kamera — periksa izin browser', 'red');
    }
}

// ============================================================
// STOP CAMERA
// ============================================================
function faStopCamera() {
    if (attCamera) { attCamera.stop(); attCamera = null; }
    if (attFaceMesh) { attFaceMesh.close(); attFaceMesh = null; }

    attCamActive    = false;
    attProcessing   = false;
    attFaceDetected = false;
    attLastState    = false;
    attStableCount  = 0;
    attAttemptCount = 0;
    attWarmingUp    = false;  // reset warm-up state

    const video = document.getElementById('fa-cam-video');
    if (video) { video.style.display = 'none'; video.srcObject = null; }

    document.getElementById('fa-cam-placeholder').style.display = 'flex';
    document.getElementById('fa-face-overlay').style.display = 'none';
    document.getElementById('fa-live-badge').style.display = 'none';

    document.getElementById('fa-btn-start').innerHTML =
        `<svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
            <path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2S13.77 8.8 12 8.8 8.8 10.23 8.8 12s1.43 3.2 3.2 3.2zm9-8.2h-2.83l-1.42-1.6H8.25L6.83 7H4C2.9 7 2 7.9 2 9v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2z"/>
         </svg> Mulai Kamera`;

    attUpdateFaceBox('none');
    attUpdateAttemptIndicator();
    faSetStatus('Tekan "Mulai Kamera" untuk mengaktifkan kamera', 'blue');
}

// ============================================================
// MEDIAPIPE RESULTS — deteksi wajah & trigger otomatis
// ============================================================
function attOnResults(results) {
    const faceCount = results.multiFaceLandmarks ? results.multiFaceLandmarks.length : 0;

    // ── Lebih dari 1 wajah terdeteksi ────────────────────────
    if (faceCount > 1) {
        attStableCount = 0;
        attFaceDetected = false;
        attLastState = false;
        attUpdateFaceBox('multiple');
        faSetStatus('Terdeteksi lebih dari 1 wajah — pastikan hanya Anda dalam frame', 'amber');
        return;
    }

    const detected = faceCount === 1;
    attFaceDetected = detected;

    if (detected !== attLastState) {
        attLastState = detected;

        if (detected) {
            attUpdateFaceBox('detected');
            faSetStatus('Wajah terdeteksi — tahan posisi...', 'green');
        } else {
            attUpdateFaceBox('none');
            attStableCount = 0;
            faSetStatus('Posisikan wajah Anda di dalam kotak', 'blue');
        }
    }

    // Hitung stabilitas — wajah harus terdeteksi terus-menerus
    // TAPI: skip jika masih dalam warm-up period (kamera belum fokus)
    if (detected && !attProcessing) {
        if (attWarmingUp) {
            // Warm-up aktif — tampilkan bahwa wajah terdeteksi tapi belum scan
            attStableCount = 0;  // reset, jangan hitung selama warm-up
            return;
        }

        attStableCount++;

        if (attStableCount >= ATT_STABLE_THRESHOLD) {
            attStableCount = 0;
            attTriggerCheckIn();
        }
    } else if (!detected) {
        attStableCount = 0;
    }
}

// ============================================================
// UPDATE FACE BOX VISUAL
// ============================================================
function attUpdateFaceBox(state) {
    const overlay = document.getElementById('fa-face-overlay');
    if (!overlay) return;

    overlay.classList.remove('fa-box-detected', 'fa-box-processing', 'fa-box-multiple');
    if (state === 'detected') overlay.classList.add('fa-box-detected');
    if (state === 'processing') overlay.classList.add('fa-box-processing');
    if (state === 'multiple') overlay.classList.add('fa-box-multiple');   // border oranye
}

// ============================================================
// ATTEMPT INDICATOR (● ● ○ — percobaan ke-N dari MAX)
// ============================================================
function attUpdateAttemptIndicator() {
    const el = document.getElementById('fa-attempt-indicator');
    if (!el) return;

    if (!attCamActive || ATT_MAX_ATTEMPTS <= 0) {
        el.style.display = 'none';
        return;
    }

    el.style.display = 'flex';
    let html = '';
    for (let i = 0; i < ATT_MAX_ATTEMPTS; i++) {
        const filled = i < attAttemptCount;
        html += `<span style="
            width:8px;height:8px;border-radius:50%;
            background:${filled ? '#ef4444' : '#d1d5db'};
            display:inline-block;margin:0 2px;
            transition:background .3s;
        "></span>`;
    }
    el.innerHTML = `<span style="font-size:11px;color:#94a3b8;margin-right:6px;">Percobaan</span>${html}`;
}

// ============================================================
// GUIDANCE MESSAGE — berdasarkan liveness score & zona
// ============================================================
function attGetGuidanceMessage(livenessScore, rejectionReason) {

    // Kasus multiple faces (dari backend)
    if (rejectionReason === 'multiple_faces_detected') {
        return {
            msg: 'Pastikan hanya 1 wajah dalam frame kamera',
            type: 'amber',
        };
    }

    // Kasus no face detected
    if (rejectionReason === 'no_face_detected') {
        return {
            msg: 'Wajah tidak terdeteksi — dekatkan ke kamera',
            type: 'amber',
        };
    }

    // ── PRIORITAS TERTINGGI: spoof_detected ──────────────────────────
    // Cek ini PERTAMA sebelum zona liveness — jangan beri hint ke spoofer
    if (rejectionReason === 'spoof_detected') {
        return {
            msg: 'Absensi ditolak — terdeteksi percobaan spoofing',
            type: 'red',
            isSpoof: true,   // flag khusus agar retry tidak diberikan
        };
    }

    // Kasus multiple faces (dari backend)
    if (rejectionReason === 'multiple_faces_detected') {
        return {
            msg: 'Pastikan hanya 1 wajah dalam frame kamera',
            type: 'amber',
            isSpoof: false,
        };
    }

    // Kasus no face detected
    if (rejectionReason === 'no_face_detected') {
        return {
            msg: 'Wajah tidak terdeteksi — dekatkan ke kamera',
            type: 'amber',
            isSpoof: false,
        };
    }

    // Kasus skor liveness tersedia — HANYA untuk genuine dengan masalah lingkungan
    // Skor ini hanya digunakan jika rejection_reason BUKAN spoof_detected
    if (livenessScore !== null && livenessScore !== undefined) {

        // Zona Ambiguous (antara floor dan threshold) — mungkin genuine + masalah lingkungan
        if (livenessScore >= ATT_AMBIGUOUS_FLOOR && livenessScore < ATT_LIVENESS_THRESHOLD) {
            if (livenessScore >= 0.40) {
                return {
                    msg: 'Hampir terverifikasi — hadap lebih lurus ke kamera',
                    type: 'amber',
                    isSpoof: false,
                };
            } else {
                return {
                    msg: 'Pastikan pencahayaan cukup, hindari cahaya dari belakang',
                    type: 'amber',
                    isSpoof: false,
                };
            }
        }
    }

    // Rejection karena confidence rendah (bukan liveness) — FaceNet gagal
    if (rejectionReason === 'low_confidence') {
        return {
            msg: 'Wajah tidak dikenali — coba posisi lebih frontal',
            type: 'amber',
            isSpoof: false,
        };
    }

    // Fallback
    return {
        msg: `Verifikasi gagal (Percobaan ${attAttemptCount}/${ATT_MAX_ATTEMPTS})`,
        type: 'red',
        isSpoof: false,
    };
}

// ============================================================
// TRIGGER CHECK-IN OTOMATIS
// ============================================================
async function attTriggerCheckIn() {
    if (attProcessing || !attCamActive) return;

    attProcessing = true;
    attUpdateFaceBox('processing');
    faSetStatus('Memindai wajah...', 'amber');

    try {
        const video = document.getElementById('fa-cam-video');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const imageBase64 = canvas.toDataURL('image/jpeg', 0.90);

        faSetStatus('Mencocokkan identitas...', 'blue');

        const response = await fetch('/attendance/check-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                image:           imageBase64,
                attack_type:     faGetAttackType(),   // label penelitian
                evaluation_mode: faGetEvalMode(),     // Sistem A atau B
            }),
        });

        const data = await response.json();

        // ── Error teknis (HTTP error, bukan rejection) ────────
        if (!data.success && data.result !== 'rejected') {
            faSetStatus(data.message ?? 'Terjadi kesalahan, coba lagi', 'red');
            attProcessing = false;
            attUpdateFaceBox('detected');
            return;
        }

        // ── DITERIMA ──────────────────────────────────────────
        if (data.result === 'accepted') {
            faSetStatus(`Absensi berhasil! ✓ (Skor: ${data.confidence_score}%)`, 'green');

            const confFill = document.getElementById('fa-conf-fill');
            const confPct = document.getElementById('fa-conf-pct');
            if (confFill) confFill.style.width = data.confidence_score + '%';
            if (confPct) confPct.textContent = data.confidence_score + '%';

            const checkinTime = document.getElementById('fa-att-checkin-time');
            if (checkinTime) checkinTime.textContent = data.time + ' WIB';

            const attStatusEl = document.getElementById('fa-att-status');
            if (attStatusEl) attStatusEl.textContent = 'Hadir';

            // Stop kamera dan reload setelah berhasil
            setTimeout(() => {
                faStopCamera();
                window.location.reload();
            }, 2000);

            return;
        }

        // ── DITOLAK — cek dulu apakah spoof atau masalah lain ──
        const livenessScore = data.liveness_score ?? null;
        const rejectionReason = data.rejection_reason ?? null;
        const guidance = attGetGuidanceMessage(livenessScore, rejectionReason);

        // ── SPOOF TERDETEKSI → langsung hentikan, tanpa retry ───
        if (guidance.isSpoof || rejectionReason === 'spoof_detected') {
            faSetStatus('Absensi ditolak — terdeteksi percobaan spoofing ⚠', 'red');
            setTimeout(() => {
                faStopCamera();
            }, 3000);
            return;
        }

        // ── Bukan spoof → lanjut logika retry ───────────────────
        attAttemptCount++;
        attUpdateAttemptIndicator();

        // Sudah mencapai max percobaan → hentikan kamera
        if (attAttemptCount >= ATT_MAX_ATTEMPTS) {
            faSetStatus(`Verifikasi gagal ${ATT_MAX_ATTEMPTS}x. Periksa kamera dan pencahayaan, lalu coba lagi.`, 'red');
            setTimeout(() => {
                faStopCamera();
            }, 3000);
            return;
        }

        // Belum max → beri jeda lalu coba lagi otomatis
        faSetStatus(guidance.msg, guidance.type);
        setTimeout(() => {
            attProcessing = false;
            attUpdateFaceBox(attFaceDetected ? 'detected' : 'none');
            if (attCamActive) {
                faSetStatus(
                    `Percobaan ${attAttemptCount}/${ATT_MAX_ATTEMPTS} — posisikan wajah Anda`,
                    'blue'
                );
            }
        }, 2500);

    } catch (e) {
        faSetStatus('Terjadi kesalahan koneksi, coba lagi', 'red');
        attProcessing = false;
        attUpdateFaceBox('none');
    }
}

// ============================================================
// STATUS BAR HELPER
// ============================================================
function faSetStatus(msg, type) {
    const bar = document.getElementById('fa-cam-status-bar');
    if (!bar) return;
    const dot = bar.querySelector('.fa-status-dot');
    const text = document.getElementById('fa-cam-status-text');

    const map = {
        blue: { bg: 'var(--blue-50)', dot: 'var(--blue-600)', text: 'var(--blue-800)' },
        green: { bg: 'var(--green-50)', dot: 'var(--green-600)', text: 'var(--green-800)' },
        amber: { bg: 'var(--amber-50)', dot: 'var(--amber-600)', text: 'var(--amber-600)' },
        red: { bg: 'var(--red-50)', dot: '#E24B4A', text: 'var(--red-600)' },
    };

    const s = map[type] || map.blue;
    bar.style.background = s.bg;
    bar.style.color = s.text;
    dot.style.background = s.dot;
    text.textContent = msg;
}

// ============================================================
// RESEARCH MODE HELPERS
// ============================================================

/**
 * Toggle visibility panel Research Mode.
 */
function faToggleResearch() {
    attResearchOpen = !attResearchOpen;
    const panel = document.getElementById('fa-research-panel');
    const chevron = document.getElementById('fa-research-chevron');
    const toggle = document.getElementById('fa-research-toggle');

    if (panel) panel.style.display = attResearchOpen ? 'block' : 'none';
    if (chevron) chevron.style.transform = attResearchOpen ? 'rotate(180deg)' : 'rotate(0deg)';
    if (toggle) {
        toggle.style.borderColor = attResearchOpen ? '#0ea5e9' : '#cbd5e1';
        toggle.style.color = attResearchOpen ? '#0369a1' : '#94a3b8';
        toggle.style.background = attResearchOpen ? '#f0f9ff' : 'none';
    }
}

/**
 * Baca nilai attack_type yang dipilih saat ini.
 * Default: 'genuine' jika panel tidak dibuka / tidak ada pilihan.
 */
function faGetAttackType() {
    const radio = document.querySelector('input[name="fa_attack_type"]:checked');
    return radio ? radio.value : 'genuine';
}

/**
 * Update highlight pada label radio dan teks indikator aktif.
 */
function faAttackTypeChange(input) {
    const allValues = ['genuine', 'print_photo', 'digital_screen'];
    const labelMap = { genuine: 'Genuine', print_photo: 'Print Photo', digital_screen: 'Digital Screen' };
    const borderMap = { genuine: '#0ea5e9', print_photo: '#f43f5e', digital_screen: '#8b5cf6' };
    const bgMap = { genuine: '#f0f9ff', print_photo: '#fff1f2', digital_screen: '#f5f3ff' };

    // Reset semua label ke default
    allValues.forEach(v => {
        const lbl = document.getElementById('fa-label-' + v);
        if (lbl) {
            lbl.style.borderColor = '#e2e8f0';
            lbl.style.background = 'white';
        }
    });

    // Highlight yang dipilih
    const selected = document.getElementById('fa-label-' + input.value);
    if (selected) {
        selected.style.borderColor = borderMap[input.value] || '#0ea5e9';
        selected.style.background = bgMap[input.value] || '#f0f9ff';
    }

    // Update teks indikator
    const indicator = document.getElementById('fa-active-label-text');
    if (indicator) indicator.textContent = labelMap[input.value] || input.value;
}

/**
 * Baca evaluation_mode yang dipilih.
 * Default: 'full_pipeline' (Sistem B).
 */
function faGetEvalMode() {
    const radio = document.querySelector('input[name="fa_eval_mode"]:checked');
    return radio ? radio.value : 'full_pipeline';
}

/**
 * Update highlight kartu Sistem A / B dan indikator aktif.
 */
function faEvalModeChange(input) {
    const modes = ['full_pipeline', 'facenet_only'];

    const borderMap = { full_pipeline: '#0d9488', facenet_only: '#8b5cf6' };
    const bgMap     = { full_pipeline: '#f0fdfa',  facenet_only: '#f5f3ff' };
    const textMap   = {
        full_pipeline: 'Sistem B aktif — Liveness Detection + FaceNet',
        facenet_only:  'Sistem A aktif — FaceNet Only (Liveness BYPASS)',
    };
    const indicatorBg     = { full_pipeline: '#f0fdfa',  facenet_only: '#f5f3ff' };
    const indicatorBorder = { full_pipeline: '#99f6e4',   facenet_only: '#c4b5fd' };
    const indicatorColor  = { full_pipeline: '#0f766e',   facenet_only: '#5b21b6' };
    const iconColor       = { full_pipeline: '#0d9488',   facenet_only: '#7c3aed' };

    // Reset semua kartu ke default
    modes.forEach(m => {
        const lbl = document.getElementById('fa-label-mode-' + m);
        if (lbl) {
            lbl.style.borderColor = '#e2e8f0';
            lbl.style.background  = 'white';
        }
    });

    // Highlight kartu yang dipilih
    const sel = document.getElementById('fa-label-mode-' + input.value);
    if (sel) {
        sel.style.borderColor = borderMap[input.value] || '#0d9488';
        sel.style.background  = bgMap[input.value]     || '#f0fdfa';
    }

    // Update indikator bawah
    const indicator = document.getElementById('fa-eval-mode-indicator');
    const modeText  = document.getElementById('fa-eval-mode-text');
    const modeIcon  = indicator ? indicator.querySelector('svg') : null;

    if (indicator) {
        indicator.style.background   = indicatorBg[input.value]     || '#f0fdfa';
        indicator.style.borderColor  = indicatorBorder[input.value] || '#99f6e4';
        indicator.style.color        = indicatorColor[input.value]  || '#0f766e';
    }
    if (modeIcon) modeIcon.style.fill = iconColor[input.value] || '#0d9488';
    if (modeText) modeText.textContent = textMap[input.value]  || input.value;
}

// ============================================================
// EXPOSE KE GLOBAL
// ============================================================
window.faStartCamera      = faStartCamera;
window.faStopCamera       = faStopCamera;
window.faToggleResearch   = faToggleResearch;
window.faAttackTypeChange = faAttackTypeChange;
window.faEvalModeChange   = faEvalModeChange;