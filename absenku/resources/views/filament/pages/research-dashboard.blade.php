<x-filament-panels::page>

{{-- ══════════════════════════════════════════════════════════
     STYLES — Semua pakai CSS kustom, bebas Tailwind compile
══════════════════════════════════════════════════════════ --}}
<style>
    /* ── Layout Grid ─────────────────────────────────────── */
    .rd-grid-4 { display:grid; grid-template-columns:repeat(2,1fr); gap:1rem; margin-bottom:1.5rem; }
    .rd-grid-2 { display:grid; grid-template-columns:1fr; gap:1.5rem; margin-bottom:1.5rem; }
    @media(min-width:768px)  { .rd-grid-4 { grid-template-columns:repeat(4,1fr); } }
    @media(min-width:1024px) { .rd-grid-2 { grid-template-columns:1fr 1fr; } }
    .rd-full    { margin-bottom:1.5rem; }

    /* ── Card ────────────────────────────────────────────── */
    .rd-card {
        background:white; border-radius:.875rem;
        box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);
        border:1px solid rgba(0,0,0,.06); overflow:hidden;
    }
    .dark .rd-card { background:rgb(30 41 59); border-color:rgba(255,255,255,.08); }
    .rd-card-body  { padding:1.25rem; }
    .rd-card-header {
        padding:.875rem 1.25rem;
        border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .dark .rd-card-header { border-color:#334155; }
    .rd-card-title {
        font-size:.875rem; font-weight:700; color:#0f172a;
        display:flex; align-items:center; gap:.5rem; margin:0;
    }
    .dark .rd-card-title { color:#f1f5f9; }

    /* ── Metric Card ─────────────────────────────────────── */
    .rd-metric {
        padding:1.25rem; border-radius:.875rem;
        display:flex; flex-direction:column; gap:.5rem;
        position:relative; overflow:hidden;
    }
    .rd-metric-icon {
        width:2.5rem; height:2.5rem; border-radius:.625rem;
        display:flex; align-items:center; justify-content:center;
        margin-bottom:.25rem;
    }
    .rd-metric-value { font-size:1.75rem; font-weight:800; line-height:1; }
    .rd-metric-label { font-size:.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.05em; }
    .rd-metric-sub   { font-size:.75rem; color:#94a3b8; margin-top:.25rem; }
    .dark .rd-metric-label { color:#94a3b8; }
    .dark .rd-metric-sub   { color:#64748b; }

    /* Warna per metrik */
    .rd-m-rose   { background:white; border:1px solid #ffe4e6; }
    .rd-m-amber  { background:white; border:1px solid #fef3c7; }
    .rd-m-purple { background:white; border:1px solid #ede9fe; }
    .rd-m-teal   { background:white; border:1px solid #ccfbf1; }
    .dark .rd-m-rose   { background:rgb(30 41 59); border-color:rgba(244,63,94,.2); }
    .dark .rd-m-amber  { background:rgb(30 41 59); border-color:rgba(217,119,6,.2); }
    .dark .rd-m-purple { background:rgb(30 41 59); border-color:rgba(139,92,246,.2); }
    .dark .rd-m-teal   { background:rgb(30 41 59); border-color:rgba(13,148,136,.2); }

    .rd-icon-rose   { background:#fff1f2; color:#f43f5e; }
    .rd-icon-amber  { background:#fef3c7; color:#d97706; }
    .rd-icon-purple { background:#f5f3ff; color:#7c3aed; }
    .rd-icon-teal   { background:#ccfbf1; color:#0d9488; }
    .dark .rd-icon-rose   { background:rgba(244,63,94,.15); }
    .dark .rd-icon-amber  { background:rgba(217,119,6,.15); }
    .dark .rd-icon-purple { background:rgba(124,58,237,.15); }
    .dark .rd-icon-teal   { background:rgba(13,148,136,.15); }

    .rd-val-rose   { color:#f43f5e; }
    .rd-val-amber  { color:#d97706; }
    .rd-val-purple { color:#7c3aed; }
    .rd-val-teal   { color:#0d9488; }

    /* Badge sistem */
    .rd-badge {
        font-size:.65rem; font-weight:700; padding:.15rem .5rem;
        border-radius:99px; letter-spacing:.04em; text-transform:uppercase;
    }
    .rd-badge-a { background:#e0f2fe; color:#0369a1; }
    .rd-badge-b { background:#dcfce7; color:#166534; }
    .dark .rd-badge-a { background:rgba(3,105,161,.2); color:#38bdf8; }
    .dark .rd-badge-b { background:rgba(22,101,52,.2); color:#4ade80; }

    /* ── Filter bar ─────────────────────────────────────── */
    .rd-filter-bar {
        display:flex; flex-wrap:wrap; align-items:center; gap:.75rem;
        padding:1rem 1.25rem;
        background:white; border-radius:.875rem;
        border:1px solid rgba(0,0,0,.06);
        box-shadow:0 1px 3px rgba(0,0,0,.04);
        margin-bottom:1.5rem;
    }
    .dark .rd-filter-bar { background:rgb(30 41 59); border-color:rgba(255,255,255,.08); }
    .rd-filter-label { font-size:.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.05em; }
    .dark .rd-filter-label { color:#94a3b8; }
    .rd-select {
        padding:.4rem .75rem; border-radius:.5rem;
        border:1.5px solid #e2e8f0; font-size:.83rem;
        background:#f8fafc; color:#334155; cursor:pointer;
    }
    .dark .rd-select { background:#0f172a; color:#cbd5e1; border-color:#334155; }
    .rd-select:focus { outline:none; border-color:#0d9488; }

    /* ── Comparison table ───────────────────────────────── */
    .rd-table { width:100%; border-collapse:collapse; font-size:.83rem; }
    .rd-table th {
        padding:.65rem 1rem; text-align:left;
        font-size:.7rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase;
        color:#64748b; background:#f8fafc;
        border-bottom:2px solid #e2e8f0;
    }
    .dark .rd-table th { color:#94a3b8; background:rgb(15 23 42); border-color:#334155; }
    .rd-table td { padding:.75rem 1rem; border-bottom:1px solid #f1f5f9; color:#334155; }
    .dark .rd-table td { border-color:#1e293b; color:#cbd5e1; }
    .rd-table tr:last-child td { border-bottom:none; }
    .rd-table tr:hover td { background:#f8fafc; }
    .dark .rd-table tr:hover td { background:rgba(15,23,42,.5); }

    .td-rose   { color:#f43f5e; font-weight:700; font-family:monospace; }
    .td-amber  { color:#d97706; font-weight:700; font-family:monospace; }
    .td-purple { color:#7c3aed; font-weight:700; font-family:monospace; }
    .td-teal   { color:#0d9488; font-weight:700; font-family:monospace; }
    .td-green  { color:#16a34a; font-weight:700; font-family:monospace; }
    .td-blue   { color:#0369a1; font-weight:700; font-family:monospace; }

    /* ── Log table ────────────────────────────────────────── */
    .rd-status-accepted { background:#dcfce7;color:#166534;padding:.2rem .6rem;border-radius:99px;font-size:.7rem;font-weight:700; }
    .rd-status-rejected { background:#fee2e2;color:#991b1b;padding:.2rem .6rem;border-radius:99px;font-size:.7rem;font-weight:700; }
    .rd-attack-badge    { background:#ede9fe;color:#6d28d9;padding:.2rem .6rem;border-radius:99px;font-size:.7rem;font-weight:700; }
    .rd-genuine-badge   { background:#e0f2fe;color:#0369a1;padding:.2rem .6rem;border-radius:99px;font-size:.7rem;font-weight:700; }
    .rd-mode-a          { background:#fef3c7;color:#b45309;padding:.15rem .5rem;border-radius:99px;font-size:.65rem;font-weight:700; }
    .rd-mode-b          { background:#ccfbf1;color:#0f766e;padding:.15rem .5rem;border-radius:99px;font-size:.65rem;font-weight:700; }
    .dark .rd-status-accepted { background:rgba(22,163,74,.2);color:#4ade80; }
    .dark .rd-status-rejected { background:rgba(239,68,68,.2);color:#f87171; }

    /* ── Chart container ─────────────────────────────────── */
    .rd-chart-wrap { position:relative; height:280px; padding:1rem; }

    /* ── EER indicator ───────────────────────────────────── */
    .rd-eer-note { font-size:.75rem;color:#64748b;text-align:center;margin-top:.5rem; }
    .dark .rd-eer-note { color:#94a3b8; }

    /* ── Section header ──────────────────────────────────── */
    .rd-section-bar {
        display:flex; align-items:center; gap:.5rem;
        font-size:.75rem; font-weight:600; color:#64748b;
        text-transform:uppercase; letter-spacing:.06em;
        margin-bottom:1rem;
    }
    .dark .rd-section-bar { color:#94a3b8; }
    .rd-section-line { flex:1; height:1px; background:#e2e8f0; }
    .dark .rd-section-line { background:#334155; }

    /* ── Misc ────────────────────────────────────────────── */
    .rd-overflow  { overflow-x:auto; }
    .rd-nowrap    { white-space:nowrap; }
    .rd-mono      { font-family:ui-monospace,monospace; font-size:.83rem; }
    .rd-muted     { color:#94a3b8; font-size:.8rem; }
    .dark .rd-muted { color:#64748b; }
    .rd-subtitle  { font-size:.875rem; color:#64748b; margin-bottom:1.5rem; }
    .dark .rd-subtitle { color:#94a3b8; }
</style>

{{-- ══════════════════════════════════════════════════════════
     SUBTITLE
══════════════════════════════════════════════════════════ --}}
<p class="rd-subtitle">
    Evaluasi performa <strong>Sistem A</strong> (FaceNet Only) vs
    <strong>Sistem B</strong> (FaceNet + MiniFASNetV2-SE Liveness Detection)
</p>

@php
    $metrics   = $this->getMetrics();
    $stats     = $this->getSummaryStats();
    $chart     = $this->getChartData();
    $table     = $this->getComparisonTable();
    $logs      = $this->getRecentLogs();
    $activeThresholds = $this->getActiveThresholds();
    $chartJson = json_encode($chart);
@endphp

{{-- Data chart disimpan di data-attribute agar Livewire update saat re-render --}}
<div id="rd-chart-data"
     data-chart="{{ $chartJson }}"
     style="display:none"></div>

{{-- ══════════════════════════════════════════════════════════
     FILTER BAR
══════════════════════════════════════════════════════════ --}}
<div class="rd-filter-bar">
    <span class="rd-filter-label">Filter:</span>

    <div style="display:flex;align-items:center;gap:.4rem;">
        <label class="rd-filter-label" for="rd-mode">Mode</label>
        <select id="rd-mode" class="rd-select" wire:model.live="filterMode">
            <option value="all">Semua</option>
            <option value="full_pipeline">Sistem B (Full Pipeline)</option>
            <option value="facenet_only">Sistem A (FaceNet Only)</option>
        </select>
    </div>

    <div style="display:flex;align-items:center;gap:.4rem;">
        <label class="rd-filter-label" for="rd-th">Threshold FaceNet</label>
        <select id="rd-th" class="rd-select" wire:model.live="filterThreshold">
            <option value="">Semua</option>
            @foreach(array_unique(array_column($table, 'threshold')) as $th)
                <option value="{{ $th }}">{{ $th }}</option>
            @endforeach
        </select>
    </div>

    <div style="margin-left:auto;display:flex;gap:.5rem;align-items:center;font-size:.78rem;color:#64748b;">
        <span>Aktif: FaceNet <strong style="color:#d97706">{{ $activeThresholds['facenet'] }}</strong></span>
        <span>·</span>
        <span>Liveness <strong style="color:#0d9488">{{ $activeThresholds['liveness'] }}</strong></span>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 1 — 4 METRIC CARDS (FAR A, FAR B, FRR A, FRR B)
══════════════════════════════════════════════════════════ --}}
<div class="rd-section-bar">
    <span>Metrik Sistem A vs Sistem B</span>
    <div class="rd-section-line"></div>
</div>

<div class="rd-grid-4" style="grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1rem;">
    {{-- FAR Sistem A --}}
    <div class="rd-metric rd-m-rose">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div class="rd-metric-icon rd-icon-rose">
                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <span class="rd-badge rd-badge-a">Sistem A</span>
        </div>
        <div class="rd-metric-value rd-val-rose">{{ $metrics['system_a']['far'] }}%</div>
        <div class="rd-metric-label">FAR — False Acceptance Rate</div>
        <div class="rd-metric-sub">Serangan yang lolos sistem (tanpa liveness)</div>
    </div>

    {{-- FAR Sistem B --}}
    <div class="rd-metric rd-m-rose">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div class="rd-metric-icon rd-icon-rose">
                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <span class="rd-badge rd-badge-b">Sistem B</span>
        </div>
        <div class="rd-metric-value rd-val-rose">{{ $metrics['system_b']['far'] }}%</div>
        <div class="rd-metric-label">FAR — False Acceptance Rate</div>
        <div class="rd-metric-sub">Serangan yang lolos sistem (dengan liveness)</div>
    </div>

    {{-- FRR Sistem A --}}
    <div class="rd-metric rd-m-amber">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div class="rd-metric-icon rd-icon-amber">
                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <span class="rd-badge rd-badge-a">Sistem A</span>
        </div>
        <div class="rd-metric-value rd-val-amber">{{ $metrics['system_a']['frr'] }}%</div>
        <div class="rd-metric-label">FRR — False Rejection Rate</div>
        <div class="rd-metric-sub">User asli yang salah ditolak (tanpa liveness)</div>
    </div>

    {{-- FRR Sistem B --}}
    <div class="rd-metric rd-m-amber">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div class="rd-metric-icon rd-icon-amber">
                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <span class="rd-badge rd-badge-b">Sistem B</span>
        </div>
        <div class="rd-metric-value rd-val-amber">{{ $metrics['system_b']['frr'] }}%</div>
        <div class="rd-metric-label">FRR — False Rejection Rate</div>
        <div class="rd-metric-sub">User asli yang salah ditolak (dengan liveness)</div>
    </div>
</div>

{{-- APCER & BPCER — Sistem B Only --}}
<div class="rd-section-bar">
    <span>Metrik Liveness Detection (Sistem B Only)</span>
    <div class="rd-section-line"></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
    {{-- APCER --}}
    <div class="rd-metric rd-m-purple">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div class="rd-metric-icon rd-icon-purple">
                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </div>
            <span style="font-size:.65rem;font-weight:700;background:#f5f3ff;color:#6d28d9;padding:.15rem .5rem;border-radius:99px;">ISO 30107-3</span>
        </div>
        <div class="rd-metric-value rd-val-purple">{{ $metrics['system_b']['apcer'] ?? '–' }}%</div>
        <div class="rd-metric-label">APCER — Attack Presentation Error</div>
        <div class="rd-metric-sub">Spoofing yang LOLOS liveness (dianggap wajah asli)</div>
    </div>

    {{-- BPCER --}}
    <div class="rd-metric rd-m-teal">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div class="rd-metric-icon rd-icon-teal">
                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span style="font-size:.65rem;font-weight:700;background:#f5f3ff;color:#6d28d9;padding:.15rem .5rem;border-radius:99px;">ISO 30107-3</span>
        </div>
        <div class="rd-metric-value rd-val-teal">{{ $metrics['system_b']['bpcer'] ?? '–' }}%</div>
        <div class="rd-metric-label">BPCER — Bona Fide Presentation Error</div>
        <div class="rd-metric-sub">Wajah asli yang DITOLAK liveness (dianggap spoof)</div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 2 — DUA CHART
══════════════════════════════════════════════════════════ --}}
<div class="rd-section-bar">
    <span>Grafik Performa vs Threshold</span>
    <div class="rd-section-line"></div>
</div>

<div class="rd-grid-2">
    {{-- Chart 1: FAR & FRR vs FaceNet Threshold --}}
    <div class="rd-card">
        <div class="rd-card-header">
            <h3 class="rd-card-title">
                <svg style="width:1rem;height:1rem;flex-shrink:0;color:#f43f5e" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                FAR & FRR vs Threshold
            </h3>
            <span style="font-size:.72rem;color:#94a3b8;">Sistem A & B</span>
        </div>
        <div class="rd-chart-wrap">
            <canvas id="chart-far-frr"></canvas>
        </div>
        <p class="rd-eer-note">Titik perpotongan FAR & FRR = Equal Error Rate (EER) yang optimal</p>
    </div>

    {{-- Chart 2: APCER & BPCER vs Liveness Threshold --}}
    <div class="rd-card">
        <div class="rd-card-header">
            <h3 class="rd-card-title">
                <svg style="width:1rem;height:1rem;flex-shrink:0;color:#7c3aed" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                APCER & BPCER — Sistem B
            </h3>
            <span style="font-size:.72rem;color:#94a3b8;">Liveness Detection</span>
        </div>
        <div class="rd-chart-wrap">
            <canvas id="chart-apcer-bpcer"></canvas>
        </div>
        <p class="rd-eer-note">APCER↓ artinya spoofing makin terdeteksi · BPCER↓ artinya genuine makin diterima</p>
    </div>
</div>

{{-- ACER card — tambahan pelengkap APCER & BPCER --}}
@php
    $apcer = $metrics['system_b']['apcer'] ?? null;
    $bpcer = $metrics['system_b']['bpcer'] ?? null;
    $acer  = ($apcer !== null && $bpcer !== null) ? round(($apcer + $bpcer) / 2, 2) : null;
@endphp
<div style="margin-bottom:1.5rem;">
    <div class="rd-metric" style="border:1px solid #e0f2fe;background:white;display:flex;flex-direction:row;align-items:center;justify-content:space-between;gap:1.5rem;padding:1rem 1.5rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <div class="rd-metric-icon" style="background:#e0f2fe;color:#0369a1;width:3rem;height:3rem;">
                <svg style="width:1.4rem;height:1.4rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 20h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:1.5rem;font-weight:800;color:#0369a1;">{{ $acer !== null ? $acer.'%' : '–' }}</div>
                <div class="rd-metric-label">ACER — Average Classification Error Rate</div>
                <div class="rd-metric-sub">(APCER + BPCER) / 2 · Sistem B · Standar ISO/IEC 30107-3</div>
            </div>
        </div>
        <div style="text-align:right;font-size:.78rem;color:#94a3b8;min-width:140px;">
            <div>APCER: <span style="color:#7c3aed;font-weight:700;">{{ $apcer !== null ? $apcer.'%' : '–' }}</span></div>
            <div style="margin-top:.25rem;">BPCER: <span style="color:#0d9488;font-weight:700;">{{ $bpcer !== null ? $bpcer.'%' : '–' }}</span></div>
            <div style="margin-top:.5rem;font-size:.7rem;background:#e0f2fe;color:#0369a1;padding:.2rem .5rem;border-radius:99px;display:inline-block;">ISO 30107-3</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     CONFUSION MATRIX — Sistem A vs Sistem B
══════════════════════════════════════════════════════════ --}}
<div class="rd-section-bar">
    <span>Confusion Matrix</span>
    <div class="rd-section-line"></div>
</div>

@php
    // ── Sistem A (facenet_only) ──────────────────────────────
    $cmA_tp = \App\Models\AttendanceLog::query()->byMode('facenet_only')->genuine()->accepted()->count();
    $cmA_fn = \App\Models\AttendanceLog::query()->byMode('facenet_only')->genuine()->rejected()->count();
    $cmA_fp = \App\Models\AttendanceLog::query()->byMode('facenet_only')->attack()->accepted()->count();
    $cmA_tn = \App\Models\AttendanceLog::query()->byMode('facenet_only')->attack()->rejected()->count();
    $cmA_total = $cmA_tp + $cmA_fn + $cmA_fp + $cmA_tn;

    // ── Sistem B (full_pipeline) ─────────────────────────────
    $cmB_tp = \App\Models\AttendanceLog::query()->byMode('full_pipeline')->genuine()->accepted()->count();
    $cmB_fn = \App\Models\AttendanceLog::query()->byMode('full_pipeline')->genuine()->rejected()->count();
    $cmB_fp = \App\Models\AttendanceLog::query()->byMode('full_pipeline')->attack()->accepted()->count();
    $cmB_tn = \App\Models\AttendanceLog::query()->byMode('full_pipeline')->attack()->rejected()->count();
    $cmB_total = $cmB_tp + $cmB_fn + $cmB_fp + $cmB_tn;

    // Akurasi keseluruhan
    $accA = $cmA_total > 0 ? round(($cmA_tp + $cmA_tn) / $cmA_total * 100, 1) : null;
    $accB = $cmB_total > 0 ? round(($cmB_tp + $cmB_tn) / $cmB_total * 100, 1) : null;
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">

    {{-- Confusion Matrix Sistem A --}}
    <div class="rd-card">
        <div class="rd-card-header">
            <h3 class="rd-card-title">
                <svg style="width:1rem;height:1rem;flex-shrink:0;color:#b45309" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Confusion Matrix — Sistem A
            </h3>
            <span class="rd-badge rd-badge-a">FaceNet Only</span>
        </div>
        <div class="rd-card-body">
            {{-- Label kolom --}}
            <div style="display:grid;grid-template-columns:120px 1fr 1fr;gap:.5rem;margin-bottom:.4rem;">
                <div></div>
                <div style="text-align:center;font-size:.7rem;font-weight:700;color:#64748b;text-transform:uppercase;">Prediksi: TERIMA</div>
                <div style="text-align:center;font-size:.7rem;font-weight:700;color:#64748b;text-transform:uppercase;">Prediksi: TOLAK</div>
            </div>
            {{-- TP & FN --}}
            <div style="display:grid;grid-template-columns:120px 1fr 1fr;gap:.5rem;margin-bottom:.5rem;align-items:center;">
                <div style="font-size:.72rem;font-weight:600;color:#64748b;">Aktual: GENUINE</div>
                <div style="background:#dcfce7;border:2px solid #16a34a;border-radius:.625rem;padding:1rem;text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:#15803d;">{{ $cmA_tp }}</div>
                    <div style="font-size:.65rem;font-weight:700;color:#16a34a;margin-top:.2rem;">TP (True Positive)</div>
                    <div style="font-size:.62rem;color:#86efac;">Genuine diterima ✓</div>
                </div>
                <div style="background:#fff1f2;border:2px solid #f43f5e;border-radius:.625rem;padding:1rem;text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:#e11d48;">{{ $cmA_fn }}</div>
                    <div style="font-size:.65rem;font-weight:700;color:#f43f5e;margin-top:.2rem;">FN (False Negative)</div>
                    <div style="font-size:.62rem;color:#fca5a5;">Genuine ditolak ✗</div>
                </div>
            </div>
            {{-- FP & TN --}}
            <div style="display:grid;grid-template-columns:120px 1fr 1fr;gap:.5rem;align-items:center;">
                <div style="font-size:.72rem;font-weight:600;color:#64748b;">Aktual: ATTACK</div>
                <div style="background:#fff1f2;border:2px solid #f43f5e;border-radius:.625rem;padding:1rem;text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:#e11d48;">{{ $cmA_fp }}</div>
                    <div style="font-size:.65rem;font-weight:700;color:#f43f5e;margin-top:.2rem;">FP (False Positive)</div>
                    <div style="font-size:.62rem;color:#fca5a5;">Attack lolos ✗</div>
                </div>
                <div style="background:#dcfce7;border:2px solid #16a34a;border-radius:.625rem;padding:1rem;text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:#15803d;">{{ $cmA_tn }}</div>
                    <div style="font-size:.65rem;font-weight:700;color:#16a34a;margin-top:.2rem;">TN (True Negative)</div>
                    <div style="font-size:.62rem;color:#86efac;">Attack diblokir ✓</div>
                </div>
            </div>
            {{-- Akurasi --}}
            @if($accA !== null)
            <div style="margin-top:1rem;padding:.6rem 1rem;background:#f8fafc;border-radius:.5rem;text-align:center;font-size:.8rem;color:#334155;">
                Akurasi Keseluruhan: <strong style="color:#0369a1;font-size:1rem;">{{ $accA }}%</strong>
                <span style="color:#94a3b8;margin-left:.5rem;">({{ $cmA_total }} percobaan)</span>
            </div>
            @else
            <div style="margin-top:1rem;padding:.6rem;text-align:center;font-size:.78rem;color:#94a3b8;">Belum ada data Sistem A</div>
            @endif
        </div>
    </div>

    {{-- Confusion Matrix Sistem B --}}
    <div class="rd-card">
        <div class="rd-card-header">
            <h3 class="rd-card-title">
                <svg style="width:1rem;height:1rem;flex-shrink:0;color:#0f766e" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Confusion Matrix — Sistem B
            </h3>
            <span class="rd-badge rd-badge-b">Liveness + FaceNet</span>
        </div>
        <div class="rd-card-body">
            {{-- Label kolom --}}
            <div style="display:grid;grid-template-columns:120px 1fr 1fr;gap:.5rem;margin-bottom:.4rem;">
                <div></div>
                <div style="text-align:center;font-size:.7rem;font-weight:700;color:#64748b;text-transform:uppercase;">Prediksi: TERIMA</div>
                <div style="text-align:center;font-size:.7rem;font-weight:700;color:#64748b;text-transform:uppercase;">Prediksi: TOLAK</div>
            </div>
            {{-- TP & FN --}}
            <div style="display:grid;grid-template-columns:120px 1fr 1fr;gap:.5rem;margin-bottom:.5rem;align-items:center;">
                <div style="font-size:.72rem;font-weight:600;color:#64748b;">Aktual: GENUINE</div>
                <div style="background:#dcfce7;border:2px solid #16a34a;border-radius:.625rem;padding:1rem;text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:#15803d;">{{ $cmB_tp }}</div>
                    <div style="font-size:.65rem;font-weight:700;color:#16a34a;margin-top:.2rem;">TP (True Positive)</div>
                    <div style="font-size:.62rem;color:#86efac;">Genuine diterima ✓</div>
                </div>
                <div style="background:#fff1f2;border:2px solid #f43f5e;border-radius:.625rem;padding:1rem;text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:#e11d48;">{{ $cmB_fn }}</div>
                    <div style="font-size:.65rem;font-weight:700;color:#f43f5e;margin-top:.2rem;">FN (False Negative)</div>
                    <div style="font-size:.62rem;color:#fca5a5;">Genuine ditolak ✗</div>
                </div>
            </div>
            {{-- FP & TN --}}
            <div style="display:grid;grid-template-columns:120px 1fr 1fr;gap:.5rem;align-items:center;">
                <div style="font-size:.72rem;font-weight:600;color:#64748b;">Aktual: ATTACK</div>
                <div style="background:#fff1f2;border:2px solid #f43f5e;border-radius:.625rem;padding:1rem;text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:#e11d48;">{{ $cmB_fp }}</div>
                    <div style="font-size:.65rem;font-weight:700;color:#f43f5e;margin-top:.2rem;">FP (False Positive)</div>
                    <div style="font-size:.62rem;color:#fca5a5;">Attack lolos ✗</div>
                </div>
                <div style="background:#dcfce7;border:2px solid #16a34a;border-radius:.625rem;padding:1rem;text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:#15803d;">{{ $cmB_tn }}</div>
                    <div style="font-size:.65rem;font-weight:700;color:#16a34a;margin-top:.2rem;">TN (True Negative)</div>
                    <div style="font-size:.62rem;color:#86efac;">Attack diblokir ✓</div>
                </div>
            </div>
            {{-- Akurasi --}}
            @if($accB !== null)
            <div style="margin-top:1rem;padding:.6rem 1rem;background:#f0fdfa;border-radius:.5rem;text-align:center;font-size:.8rem;color:#334155;">
                Akurasi Keseluruhan: <strong style="color:#0d9488;font-size:1rem;">{{ $accB }}%</strong>
                <span style="color:#94a3b8;margin-left:.5rem;">({{ $cmB_total }} percobaan)</span>
            </div>
            @else
            <div style="margin-top:1rem;padding:.6rem;text-align:center;font-size:.78rem;color:#94a3b8;">Belum ada data Sistem B</div>
            @endif
        </div>
    </div>

</div>



{{-- ══════════════════════════════════════════════════════════
     ROW 3 — COMPARISON TABLE PER THRESHOLD
══════════════════════════════════════════════════════════ --}}
<div class="rd-section-bar">
    <span>Perbandingan Per Threshold</span>
    <div class="rd-section-line"></div>
</div>

<div class="rd-card rd-full">
    <div class="rd-card-header">
        <h3 class="rd-card-title">
            <svg style="width:1rem;height:1rem;flex-shrink:0;color:#0d9488" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 3v18M14 3v18"/>
            </svg>
            Tabel Perbandingan Sistem A vs Sistem B
        </h3>
    </div>
    <div class="rd-overflow">
        <table class="rd-table">
            <thead>
                <tr>
                    <th>Threshold</th>
                    <th><span class="rd-badge rd-badge-a">A</span> FAR (%)</th>
                    <th><span class="rd-badge rd-badge-a">A</span> FRR (%)</th>
                    <th><span class="rd-badge rd-badge-b">B</span> FAR (%)</th>
                    <th><span class="rd-badge rd-badge-b">B</span> FRR (%)</th>
                    <th>APCER (%)</th>
                    <th>BPCER (%)</th>
                    <th>Δ FAR</th>
                </tr>
            </thead>
            <tbody>
                @forelse($table as $row)
                <tr>
                    <td><span class="rd-mono" style="color:#0f172a;font-weight:700;">{{ $row['threshold'] }}</span></td>
                    <td><span class="td-rose">{{ $row['far_a'] }}</span></td>
                    <td><span class="td-amber">{{ $row['frr_a'] }}</span></td>
                    <td><span class="td-rose">{{ $row['far_b'] }}</span></td>
                    <td><span class="td-amber">{{ $row['frr_b'] }}</span></td>
                    <td><span class="td-purple">{{ $row['apcer'] }}</span></td>
                    <td><span class="td-teal">{{ $row['bpcer'] }}</span></td>
                    <td>
                        @php $delta = round($row['far_a'] - $row['far_b'], 2); @endphp
                        <span style="font-weight:700;color:{{ $delta >= 0 ? '#16a34a' : '#dc2626' }}">
                            {{ $delta >= 0 ? '↓ ' : '↑ ' }}{{ abs($delta) }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:2rem;color:#94a3b8;">
                        Belum ada data. Lakukan absensi terlebih dahulu untuk melihat metrik evaluasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 4 — RECENT LOGS PREVIEW
══════════════════════════════════════════════════════════ --}}
<div class="rd-section-bar">
    <span>Log Absensi Terbaru</span>
    <div class="rd-section-line"></div>
</div>

<div class="rd-card rd-full">
    <div class="rd-card-header">
        <h3 class="rd-card-title">
            <svg style="width:1rem;height:1rem;flex-shrink:0;color:#0369a1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            15 Log Terbaru
        </h3>
        <a href="{{ \Illuminate\Support\Facades\Route::has('filament.admin.resources.attendance-logs.index') ? route('filament.admin.resources.attendance-logs.index') : '#' }}"
           style="font-size:.78rem;color:#0d9488;font-weight:600;text-decoration:none;">
            Lihat Semua →
        </a>
    </div>
    <div class="rd-overflow">
        <table class="rd-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Mode</th>
                    <th>Attack Type</th>
                    <th>Result</th>
                    <th>Confidence</th>
                    <th>Liveness</th>
                    <th>Threshold</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="rd-nowrap rd-muted">
                        {{ $log->attempted_at?->format('d/m H:i') ?? '-' }}
                    </td>
                    <td>
                        <span style="font-weight:600;color:#0f172a;font-size:.83rem;">
                            {{ $log->user?->name ?? 'Unknown' }}
                        </span>
                    </td>
                    <td>
                        @if($log->evaluation_mode === 'facenet_only')
                            <span class="rd-mode-a">Sistem A</span>
                        @else
                            <span class="rd-mode-b">Sistem B</span>
                        @endif
                    </td>
                    <td>
                        @if($log->attack_type === 'genuine')
                            <span class="rd-genuine-badge">Genuine</span>
                        @else
                            <span class="rd-attack-badge">{{ ucwords(str_replace('_',' ', $log->attack_type ?? '-')) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($log->result === 'accepted')
                            <span class="rd-status-accepted">✓ Diterima</span>
                        @else
                            <span class="rd-status-rejected">✗ Ditolak</span>
                        @endif
                    </td>
                    <td class="rd-mono" style="color:#0f172a;">{{ number_format(($log->confidence_score ?? 0) * 100, 1) }}%</td>
                    <td class="rd-mono" style="color:#0d9488;">{{ number_format(($log->liveness_score ?? 0) * 100, 1) }}%</td>
                    <td class="rd-mono rd-muted">{{ number_format($log->threshold_used ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:2rem;color:#94a3b8;">
                        Belum ada log absensi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     CHART.JS — dimuat secara dinamis agar kompatibel
     dengan Livewire SPA navigation (navigate:navigated)
══════════════════════════════════════════════════════════ --}}
<script>
(function() {
    // ── Global chart instances ────────────────────────────────
    let _rdChart1 = null;
    let _rdChart2 = null;

    // ── Baca chart data dari data-attribute (diupdate Livewire morph) ─
    function rdGetData() {
        const el = document.getElementById('rd-chart-data');
        if (!el) return null;
        try { return JSON.parse(el.dataset.chart); }
        catch(e) { console.warn('[rdChart] JSON parse error', e); return null; }
    }

    // ── Inisialisasi / re-inisialisasi Chart.js ───────────────
    function rdInitCharts() {
        if (typeof Chart === 'undefined') return;
        const chartData = rdGetData();
        if (!chartData) return;

        const isDark    = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';

        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
        Chart.defaults.font.size   = 11;

        if (_rdChart1) { _rdChart1.destroy(); _rdChart1 = null; }
        if (_rdChart2) { _rdChart2.destroy(); _rdChart2 = null; }

        // Chart 1: FAR & FRR vs Threshold
        const ctx1 = document.getElementById('chart-far-frr');
        if (ctx1) {
            _rdChart1 = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        { label: 'FAR – Sistem A', data: chartData.far_a,  borderColor: '#f43f5e', backgroundColor: 'rgba(244,63,94,.1)',  borderWidth: 2.5, borderDash: [5,3], pointRadius: 4, tension: 0.3, fill: false },
                        { label: 'FAR – Sistem B', data: chartData.far_b,  borderColor: '#f43f5e', backgroundColor: 'rgba(244,63,94,.05)', borderWidth: 2,   pointRadius: 4, tension: 0.3, fill: false },
                        { label: 'FRR – Sistem A', data: chartData.frr_a,  borderColor: '#d97706', backgroundColor: 'rgba(217,119,6,.1)',  borderWidth: 2.5, borderDash: [5,3], pointRadius: 4, tension: 0.3, fill: false },
                        { label: 'FRR – Sistem B', data: chartData.frr_b,  borderColor: '#d97706', backgroundColor: 'rgba(217,119,6,.05)', borderWidth: 2,   pointRadius: 4, tension: 0.3, fill: false },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: textColor, boxWidth: 14, padding: 12 } }, tooltip: { mode: 'index', intersect: false } },
                    scales: {
                        x: { grid: { color: gridColor }, ticks: { color: textColor }, title: { display: true, text: 'Threshold FaceNet', color: textColor } },
                        y: { grid: { color: gridColor }, ticks: { color: textColor, callback: v => v + '%' }, title: { display: true, text: 'Error Rate (%)', color: textColor }, min: 0 }
                    }
                }
            });
        }

        // Chart 2: APCER & BPCER
        const ctx2 = document.getElementById('chart-apcer-bpcer');
        if (ctx2) {
            _rdChart2 = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        { label: 'APCER', data: chartData.apcer, borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,.1)', borderWidth: 2.5, pointRadius: 4, tension: 0.3, fill: false },
                        { label: 'BPCER', data: chartData.bpcer, borderColor: '#0d9488', backgroundColor: 'rgba(13,148,136,.1)', borderWidth: 2.5, pointRadius: 4, tension: 0.3, fill: false },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: textColor, boxWidth: 14, padding: 12 } }, tooltip: { mode: 'index', intersect: false } },
                    scales: {
                        x: { grid: { color: gridColor }, ticks: { color: textColor }, title: { display: true, text: 'Threshold FaceNet', color: textColor } },
                        y: { grid: { color: gridColor }, ticks: { color: textColor, callback: v => v + '%' }, title: { display: true, text: 'Error Rate (%)', color: textColor }, min: 0 }
                    }
                }
            });
        }
    }

    // ── Pastikan Chart.js tersedia, baru init ─────────────────
    // Menangani kasus Livewire SPA navigation (DOMContentLoaded tidak fire ulang)
    function rdEnsureChartJS(callback) {
        if (typeof Chart !== 'undefined') {
            callback();
            return;
        }
        // Cegah double-loading
        if (document.getElementById('rd-chartjs-cdn')) {
            const poll = setInterval(() => {
                if (typeof Chart !== 'undefined') { clearInterval(poll); callback(); }
            }, 50);
            return;
        }
        const s = document.createElement('script');
        s.id  = 'rd-chartjs-cdn';
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
        s.onload = callback;
        document.head.appendChild(s);
    }

    // ── Event listeners ───────────────────────────────────────

    // Jalankan langsung — script ini dieksekusi SETELAH canvas ada di DOM
    // (DOMContentLoaded sudah fire sebelum Livewire menjalankan script ini)
    rdEnsureChartJS(rdInitCharts);

    // Livewire SPA navigation (navigasi antar halaman admin tanpa reload)
    document.addEventListener('livewire:navigated', () => rdEnsureChartJS(rdInitCharts));

    // Filter berubah (wire:model.live) → Livewire re-render DOM, data-chart diperbarui
    document.addEventListener('livewire:updated', rdInitCharts);

})();
</script>

</x-filament-panels::page>


