<div class="fa-reg-camera-wrap">
 
    {{-- Camera card --}}
    <div class="fa-camera-card">
        <div class="fa-camera-title">
            Kamera Pendaftaran
            <span class="fa-live-badge" id="fa-reg-live-badge" style="display:none">
                <div class="fa-live-dot"></div>LIVE
            </span>
        </div>
 
        {{-- Viewport --}}
        <div class="fa-camera-viewport" id="fa-reg-viewport">
 
            {{-- Placeholder --}}
            <div class="fa-cam-placeholder" id="fa-reg-placeholder">
                <div class="fa-cam-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 11.75c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zm6 0c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8 0-.29.02-.58.05-.86 2.36-1.05 4.23-2.98 5.21-5.37C11.07 8.33 14.05 10 17.42 10c.78 0 1.53-.09 2.25-.26.21.71.33 1.47.33 2.26 0 4.41-3.59 8-8 8z"/>
                    </svg>
                </div>
                <div class="fa-cam-text">Kamera belum aktif</div>
            </div>
 
            {{-- Video --}}
            <video id="fa-reg-video" class="fa-cam-video" autoplay playsinline muted></video>
 
            {{-- Arrow overlay (pojok kiri atas) --}}
            <div class="fa-reg-arrow-overlay" id="fa-reg-arrow-overlay"></div>
 
            {{-- Face box overlay --}}
            <div class="fa-reg-face-box" id="fa-reg-face-box" style="display:none">
                <svg class="fa-reg-face-oval" viewBox="0 0 200 240" fill="none">
                    <ellipse id="fa-reg-oval" cx="100" cy="120" rx="88" ry="108"
                             stroke="rgba(255,255,255,0.4)" stroke-width="2" stroke-dasharray="6 4"/>
                </svg>
                <div class="fa-reg-face-corners" id="fa-reg-corners">
                    <div class="fa-reg-corner fa-reg-corner-tl"></div>
                    <div class="fa-reg-corner fa-reg-corner-tr"></div>
                    <div class="fa-reg-corner fa-reg-corner-bl"></div>
                    <div class="fa-reg-corner fa-reg-corner-br"></div>
                </div>
                <div class="fa-reg-scan-line" id="fa-reg-scan-line" style="display:none"></div>
            </div>
 
            {{-- Guide text overlay --}}
            <div class="fa-reg-guide-text" id="fa-reg-guide-text">
                Posisikan wajah di dalam kotak
            </div>
            <div id="fa-reg-debug" style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,0.6);color:#0f0;font-family:monospace;font-size:11px;padding:4px 8px;border-radius:6px;z-index:10;"></div>
 
            {{-- Countdown overlay --}}
            <div class="fa-reg-countdown" id="fa-reg-countdown" style="display:none">
                <span id="fa-reg-countdown-num">3</span>
            </div>
 
            {{-- Flash effect --}}
            <div class="fa-reg-flash" id="fa-reg-flash"></div>
 
        </div>
 
        {{-- Progress --}}
        <div class="fa-reg-progress-wrap" id="fa-reg-progress-wrap" style="display:none">
            <div class="fa-reg-progress-header">
                <span class="fa-reg-progress-label" id="fa-reg-progress-label">Mengambil foto 0 dari 10</span>
                <span class="fa-reg-progress-pct"   id="fa-reg-progress-pct">0%</span>
            </div>
            <div class="fa-reg-progress-bar">
                <div class="fa-reg-progress-fill" id="fa-reg-progress-fill" style="width:0%"></div>
            </div>
            <div class="fa-reg-direction" id="fa-reg-direction">Lihat lurus ke kamera</div>
        </div>
 
        {{-- Status bar --}}
        <div class="fa-cam-status-bar" id="fa-reg-status-bar">
            <div class="fa-status-dot" style="background:var(--blue-600)"></div>
            <span id="fa-reg-status-text">Tekan "Mulai Kamera" untuk memulai pendaftaran</span>
        </div>
 
        {{-- Buttons --}}
        <div class="fa-cam-buttons">
            <button class="fa-btn fa-btn-outline" id="fa-reg-btn-start" onclick="faRegStartCamera()">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2S13.77 8.8 12 8.8 8.8 10.23 8.8 12s1.43 3.2 3.2 3.2zm9-8.2h-2.83l-1.42-1.6H8.25L6.83 7H4C2.9 7 2 7.9 2 9v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2z"/>
                </svg>
                Mulai Kamera
            </button>
            <button class="fa-btn fa-btn-outline" onclick="faHideRegCamera()">
                Batal
            </button>
        </div>
 
    </div>
 
    {{-- Thumbnail preview --}}
    <div class="fa-reg-side">
        <div class="fa-card" style="height:100%">
            <div class="fa-card-header">
                <div class="fa-card-title">Foto Terambil</div>
                <span id="fa-reg-count-badge"
                      style="font-size:11px;font-weight:600;color:var(--gray-400)">0 / 10</span>
            </div>
            <div class="fa-reg-thumbs" id="fa-reg-thumbs">
                @for($i = 0; $i < 10; $i++)
                    <div class="fa-reg-thumb-slot" id="fa-reg-thumb-{{ $i }}">
                        <div class="fa-reg-thumb-empty">{{ $i + 1 }}</div>
                    </div>
                @endfor
            </div>
            <div id="fa-reg-submit-wrap" style="display:none;margin-top:14px">
                <button class="fa-btn fa-btn-primary" onclick="faRegSubmit()">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    Simpan Data Wajah
                </button>
            </div>
        </div>
    </div>
 
</div>