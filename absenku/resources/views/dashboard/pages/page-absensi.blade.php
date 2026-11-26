<div class="fa-page" id="fa-page-absensi">

    <div class="fa-page-header">
        <h2>Absensi Wajah</h2>
        <p>Posisikan wajah Anda di tengah frame. Sistem akan memproses otomatis (maks. 3 percobaan)</p>
    </div>

    <div class="fa-attendance-layout">

        {{-- Camera panel --}}
        <div class="fa-camera-card">
            <div class="fa-camera-title">
                Kamera Langsung
                <span class="fa-live-badge" id="fa-live-badge" style="display:none">
                    <div class="fa-live-dot"></div>LIVE
                </span>
            </div>

            <div class="fa-camera-viewport">
                <div class="fa-cam-placeholder" id="fa-cam-placeholder">
                    <div class="fa-cam-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2S13.77 8.8 12 8.8 8.8 10.23 8.8 12s1.43 3.2 3.2 3.2zm9-8.2h-2.83l-1.42-1.6H8.25L6.83 7H4C2.9 7 2 7.9 2 9v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm0 14H4V9h3.59l1.42-1.6h6l1.41 1.6H21v12z"/>
                        </svg>
                    </div>
                    <div class="fa-cam-text">Kamera belum aktif</div>
                </div>
                <video id="fa-cam-video" class="fa-cam-video" autoplay playsinline muted></video>
                <div class="fa-face-overlay" id="fa-face-overlay">
                    <div class="corner tl"></div>
                    <div class="corner tr"></div>
                    <div class="corner bl"></div>
                    <div class="corner br"></div>
                    <div class="fa-scan-line" id="fa-scan-line"></div>
                </div>
            </div>

            <div class="fa-cam-status-bar" id="fa-cam-status-bar">
                <div class="fa-status-dot" style="background:var(--blue-600)"></div>
                <span id="fa-cam-status-text">Tekan "Mulai Kamera" untuk mengaktifkan kamera</span>
            </div>

            {{-- Attempt indicator: ● ● ○ --}}
            <div id="fa-attempt-indicator" style="display:none;align-items:center;justify-content:center;padding:6px 12px;gap:4px;font-size:11px;"></div>

            <div class="fa-cam-buttons">
                <button class="fa-btn fa-btn-outline" id="fa-btn-start" onclick="faStartCamera()">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2S13.77 8.8 12 8.8 8.8 10.23 8.8 12s1.43 3.2 3.2 3.2zm9-8.2h-2.83l-1.42-1.6H8.25L6.83 7H4C2.9 7 2 7.9 2 9v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2z"/>
                    </svg>
                    Mulai Kamera
                </button>
            </div>

            {{-- ── Research Mode Panel ─────────────────────────── --}}
            <div style="padding:0 1rem 1rem;">
                <button onclick="faToggleResearch()" id="fa-research-toggle"
                    style="background:none;border:1px dashed #cbd5e1;border-radius:.5rem;
                           padding:.35rem .75rem;font-size:.72rem;color:#94a3b8;cursor:pointer;
                           width:100%;display:flex;align-items:center;gap:.4rem;justify-content:center;
                           transition:all .2s;">
                    <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor">
                        <path d="M19.14 12.94c.04-.3.06-.61.06-.94s-.02-.64-.07-.94l2.03-1.58a.49.49 0 0 0 .12-.61l-1.92-3.32a.49.49 0 0 0-.59-.22l-2.39.96a7.08 7.08 0 0 0-1.62-.94l-.36-2.54a.484.484 0 0 0-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58a.49.49 0 0 0-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.37 1.04.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.57 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                    </svg>
                    Mode Penelitian
                    <svg id="fa-research-chevron" viewBox="0 0 24 24"
                         style="width:12px;height:12px;fill:currentColor;margin-left:auto;transition:transform .2s;">
                        <path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </button>

                {{-- Panel isi --}}
                <div id="fa-research-panel" style="display:none;margin-top:.6rem;
                     background:#f8fafc;border:1px solid #e2e8f0;border-radius:.625rem;
                     padding:.875rem;">

                    <div style="font-size:.7rem;font-weight:700;color:#64748b;
                                text-transform:uppercase;letter-spacing:.06em;margin-bottom:.6rem;">
                        Label Jenis Percobaan
                    </div>

                    {{-- Radio buttons --}}
                    <div style="display:flex;flex-direction:column;gap:.45rem;" id="fa-attack-type-group">

                        <label id="fa-label-genuine"
                            style="display:flex;align-items:center;gap:.55rem;padding:.5rem .7rem;
                                   border-radius:.5rem;cursor:pointer;border:1.5px solid #0ea5e9;
                                   background:#f0f9ff;">
                            <input type="radio" name="fa_attack_type" value="genuine" checked
                                   onchange="faAttackTypeChange(this)"
                                   style="accent-color:#0ea5e9;">
                            <span style="font-size:.8rem;font-weight:600;color:#0369a1;">
                                ✓ Genuine
                            </span>
                            <span style="font-size:.7rem;color:#94a3b8;margin-left:auto;">Wajah asli</span>
                        </label>

                        <label id="fa-label-print_photo"
                            style="display:flex;align-items:center;gap:.55rem;padding:.5rem .7rem;
                                   border-radius:.5rem;cursor:pointer;border:1.5px solid #e2e8f0;
                                   background:white;">
                            <input type="radio" name="fa_attack_type" value="print_photo"
                                   onchange="faAttackTypeChange(this)"
                                   style="accent-color:#f43f5e;">
                            <span style="font-size:.8rem;font-weight:600;color:#be123c;">
                                ⚠ Print Photo
                            </span>
                            <span style="font-size:.7rem;color:#94a3b8;margin-left:auto;">Foto cetak</span>
                        </label>

                        <label id="fa-label-digital_screen"
                            style="display:flex;align-items:center;gap:.55rem;padding:.5rem .7rem;
                                   border-radius:.5rem;cursor:pointer;border:1.5px solid #e2e8f0;
                                   background:white;">
                            <input type="radio" name="fa_attack_type" value="digital_screen"
                                   onchange="faAttackTypeChange(this)"
                                   style="accent-color:#8b5cf6;">
                            <span style="font-size:.8rem;font-weight:600;color:#6d28d9;">
                                ⚠ Digital Screen
                            </span>
                            <span style="font-size:.7rem;color:#94a3b8;margin-left:auto;">Layar HP/PC</span>
                        </label>

                    </div>

                    {{-- Indikator label aktif --}}
                    <div style="margin-top:.6rem;padding:.4rem .6rem;border-radius:.4rem;
                                background:#fef3c7;border:1px solid #fde68a;
                                font-size:.72rem;color:#92400e;display:flex;align-items:center;gap:.4rem;">
                        <svg viewBox="0 0 24 24" style="width:12px;height:12px;fill:#d97706;flex-shrink:0">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                        Percobaan ini akan dicatat sebagai:
                        <strong id="fa-active-label-text">Genuine</strong>
                    </div>

                    {{-- ── Evaluation Mode Toggle (Sistem A / B) ────────── --}}
                    <div style="margin-top:.875rem;padding-top:.75rem;border-top:1px solid #e2e8f0;">

                        <div style="font-size:.7rem;font-weight:700;color:#64748b;
                                    text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem;">
                            Mode Evaluasi
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.45rem;">

                            {{-- Sistem B (default) --}}
                            <label id="fa-label-mode-full_pipeline"
                                style="display:flex;flex-direction:column;align-items:center;gap:.3rem;
                                       padding:.6rem .5rem;border-radius:.5rem;cursor:pointer;
                                       border:1.5px solid #0d9488;background:#f0fdfa;text-align:center;">
                                <input type="radio" name="fa_eval_mode" value="full_pipeline" checked
                                       onchange="faEvalModeChange(this)"
                                       style="display:none;">
                                <span style="font-size:.75rem;font-weight:700;color:#0f766e;">Sistem B</span>
                                <span style="font-size:.65rem;color:#5eead4;">Liveness + FaceNet</span>
                                <span style="font-size:.6rem;background:#ccfbf1;color:#0f766e;
                                             padding:.1rem .4rem;border-radius:99px;font-weight:600;">
                                    DEFAULT
                                </span>
                            </label>

                            {{-- Sistem A (bypass liveness) --}}
                            <label id="fa-label-mode-facenet_only"
                                style="display:flex;flex-direction:column;align-items:center;gap:.3rem;
                                       padding:.6rem .5rem;border-radius:.5rem;cursor:pointer;
                                       border:1.5px solid #e2e8f0;background:white;text-align:center;">
                                <input type="radio" name="fa_eval_mode" value="facenet_only"
                                       onchange="faEvalModeChange(this)"
                                       style="display:none;">
                                <span style="font-size:.75rem;font-weight:700;color:#7c3aed;">Sistem A</span>
                                <span style="font-size:.65rem;color:#a78bfa;">FaceNet Only</span>
                                <span style="font-size:.6rem;background:#ede9fe;color:#5b21b6;
                                             padding:.1rem .4rem;border-radius:99px;font-weight:600;">
                                    BYPASS LIVENESS
                                </span>
                            </label>

                        </div>

                        {{-- Indikator mode aktif --}}
                        <div id="fa-eval-mode-indicator"
                             style="margin-top:.5rem;padding:.35rem .6rem;border-radius:.4rem;
                                    background:#f0fdfa;border:1px solid #99f6e4;
                                    font-size:.7rem;color:#0f766e;display:flex;align-items:center;gap:.4rem;">
                            <svg viewBox="0 0 24 24" style="width:11px;height:11px;fill:#0d9488;flex-shrink:0">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                            <span id="fa-eval-mode-text">Sistem B aktif — Liveness Detection + FaceNet</span>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        {{-- Info panel --}}
        <div class="fa-att-info">

            <div class="fa-info-card">
                <div class="fa-info-card-title">Status Hari Ini</div>
                <div class="fa-today-status">
                    @if($todayLog)
                        <div class="fa-status-icon hadir">
                            <svg viewBox="0 0 24 24" fill="var(--green-600)">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fa-today-status-text">Hadir</div>
                            <div class="fa-today-status-sub">
                                {{ \Carbon\Carbon::parse($todayLog->attempted_at)->format('H:i') }} WIB
                            </div>
                        </div>
                    @else
                        <div class="fa-status-icon belum">
                            <svg viewBox="0 0 24 24" fill="var(--amber-600)">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fa-today-status-text" style="color:var(--amber-600)">Belum Absen</div>
                            <div class="fa-today-status-sub" id="fa-att-date"></div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="fa-info-card">
                <div class="fa-info-card-title">Catatan Waktu</div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Check In</span>
                    <span class="fa-time-val" id="fa-att-checkin-time">
                        {{ $todayLog ? \Carbon\Carbon::parse($todayLog->attempted_at)->format('H:i').' WIB' : '—' }}
                    </span>
                </div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Check Out</span>
                    <span class="fa-time-val">
                        {{ $todayLog && $todayLog->checked_out_at ? \Carbon\Carbon::parse($todayLog->checked_out_at)->format('H:i').' WIB' : '—' }}
                    </span>
                </div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Total Jam</span>
                    <span class="fa-time-val">
                        @if($todayLog && $todayLog->checked_out_at)
                            {{ \Carbon\Carbon::parse($todayLog->attempted_at)->diff(\Carbon\Carbon::parse($todayLog->checked_out_at))->format('%H:%I') }} jam
                        @else
                            —
                        @endif
                    </span>
                </div>
            </div>

            <div class="fa-info-card">
                <div class="fa-info-card-title">Skor Kepercayaan</div>
                <div style="margin-bottom:8px;font-size:12px;color:var(--gray-600)">Identifikasi Wajah</div>
                @php $score = $todayLog ? round($todayLog->confidence_score * 100, 1) : 0; @endphp
                <div class="fa-conf-score">
                    <div class="fa-conf-bar">
                        <div class="fa-conf-fill" id="fa-conf-fill" style="width:{{ $score }}%"></div>
                    </div>
                    <div class="fa-conf-pct" id="fa-conf-pct">{{ $score > 0 ? $score.'%' : '—' }}</div>
                </div>
                <div style="margin-top:8px;font-size:11px;color:var(--gray-400)">Model FaceNet · Terverifikasi</div>
            </div>

            <div class="fa-info-card">
                <div class="fa-info-card-title">Statistik Bulan Ini</div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Total Hadir</span>
                    <span class="fa-time-val">{{ $totalHadir }} hari</span>
                </div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Terlambat</span>
                    <span class="fa-time-val">{{ $totalTerlambat }} hari</span>
                </div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Persentase</span>
                    <span class="fa-time-val" style="color:var(--green-600)">
                        {{ \Carbon\Carbon::now()->day > 0 ? number_format(($totalHadir / \Carbon\Carbon::now()->day) * 100, 1) : 0 }}%
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>