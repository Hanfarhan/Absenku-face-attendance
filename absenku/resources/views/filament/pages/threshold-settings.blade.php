<x-filament-panels::page>

    {{-- ══════════════════════════════════════════════════════════
         CUSTOM STYLES — CoachPro inspired light theme
         Semua ukuran pakai CSS class kustom, BUKAN Tailwind utility
         agar tidak bergantung pada proses kompilasi Vite/Tailwind
    ══════════════════════════════════════════════════════════ --}}
    <style>
        /* ── Icon ukuran ──────────────────────────────────────── */
        .th-icon-sm { width: 1.125rem; height: 1.125rem; flex-shrink: 0; }
        .th-icon-md { width: 1.25rem;  height: 1.25rem;  flex-shrink: 0; }
        .th-icon-xs { width: 0.875rem; height: 0.875rem; flex-shrink: 0; }
        .th-icon-amber { color: #f59e0b; }
        .th-icon-teal  { color: #14b8a6; }

        /* ── Card dasar ───────────────────────────────────────── */
        .th-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
            padding: 1.5rem;
            border: 1px solid rgba(0,0,0,.06);
            transition: box-shadow .2s;
        }
        .dark .th-card {
            background: rgb(30 41 59);
            border-color: rgba(255,255,255,.08);
        }
        .th-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); }

        /* ── Badge nilai aktif ────────────────────────────────── */
        .th-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 3.5rem; height: 3.5rem;
            border-radius: 50%;
            font-size: 1rem; font-weight: 700;
            flex-shrink: 0;
        }
        .th-badge-teal  { background: #ccfbf1; color: #0f766e; }
        .th-badge-amber { background: #fef3c7; color: #b45309; }
        .dark .th-badge-teal  { background: #134e4a; color: #5eead4; }
        .dark .th-badge-amber { background: #451a03; color: #fcd34d; }

        /* ── Slider ───────────────────────────────────────────── */
        .th-slider {
            -webkit-appearance: none;
            width: 100%; height: 6px;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
            transition: opacity .15s;
        }
        .th-slider:hover { opacity: .9; }

        .th-slider-teal {
            background: linear-gradient(
                to right,
                #0d9488 0%,
                #0d9488 var(--val, 50%),
                #e2e8f0 var(--val, 50%),
                #e2e8f0 100%
            );
        }
        .dark .th-slider-teal {
            background: linear-gradient(
                to right,
                #0d9488 0%,
                #0d9488 var(--val, 50%),
                #334155 var(--val, 50%),
                #334155 100%
            );
        }

        .th-slider-amber {
            background: linear-gradient(
                to right,
                #d97706 0%,
                #d97706 var(--val, 75%),
                #e2e8f0 var(--val, 75%),
                #e2e8f0 100%
            );
        }
        .dark .th-slider-amber {
            background: linear-gradient(
                to right,
                #d97706 0%,
                #d97706 var(--val, 75%),
                #334155 var(--val, 75%),
                #334155 100%
            );
        }

        .th-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px; height: 18px;
            border-radius: 50%;
            background: white;
            border: 2.5px solid currentColor;
            box-shadow: 0 1px 4px rgba(0,0,0,.2);
            cursor: pointer;
        }
        .th-slider-teal::-webkit-slider-thumb  { color: #0d9488; }
        .th-slider-amber::-webkit-slider-thumb { color: #d97706; }

        /* ── Label trade-off ──────────────────────────────────── */
        .trade-label {
            font-size: .7rem; font-weight: 600;
            letter-spacing: .04em; text-transform: uppercase;
            color: #94a3b8;
        }
        .dark .trade-label { color: #64748b; }

        /* ── Section heading ──────────────────────────────────── */
        .th-section-title {
            font-size: 1rem; font-weight: 700;
            color: #0f172a;
            display: flex; align-items: center; gap: .5rem;
            margin: 0;
        }
        .dark .th-section-title { color: #f1f5f9; }

        /* ── History table ────────────────────────────────────── */
        .th-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
        .th-table th {
            text-align: left; padding: .6rem 1rem;
            font-size: .7rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            color: #64748b; border-bottom: 1px solid #e2e8f0;
        }
        .dark .th-table th { color: #94a3b8; border-color: #334155; }
        .th-table td { padding: .7rem 1rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .dark .th-table td { border-color: #1e293b; color: #cbd5e1; }
        .th-table tr:last-child td { border-bottom: none; }
        .th-table tr:hover td { background: #f8fafc; }
        .dark .th-table tr:hover td { background: #1e293b; }

        .badge-active   { background:#dcfce7; color:#16a34a; border-radius:99px; padding:.15rem .6rem; font-size:.7rem; font-weight:600; }
        .badge-inactive { background:#f1f5f9; color:#94a3b8; border-radius:99px; padding:.15rem .6rem; font-size:.7rem; font-weight:600; }
        .dark .badge-active   { background:#14532d; color:#4ade80; }
        .dark .badge-inactive { background:#1e293b; color:#475569; }

        /* ── Input angka ──────────────────────────────────────── */
        .th-input {
            width: 90px; padding: .4rem .6rem;
            border: 1.5px solid #e2e8f0; border-radius: .5rem;
            font-size: .9rem; font-weight: 600; text-align: center;
            background: #f8fafc; color: #0f172a;
            transition: border-color .2s;
        }
        .th-input:focus { outline: none; }
        .dark .th-input { background: #0f172a; color: #f1f5f9; border-color: #334155; }

        .th-note-input {
            width: 100%; padding: .45rem .75rem;
            border: 1.5px solid #e2e8f0; border-radius: .5rem;
            font-size: .85rem; background: #f8fafc; color: #334155;
            transition: border-color .2s;
        }
        .th-note-input:focus { outline: none; }
        .dark .th-note-input { background: #0f172a; color: #cbd5e1; border-color: #334155; }

        /* ── Button ───────────────────────────────────────────── */
        .th-btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .55rem 1.25rem; border-radius: .6rem;
            font-size: .85rem; font-weight: 600;
            cursor: pointer; border: none; transition: all .2s;
            white-space: nowrap;
        }
        .th-btn-teal  { background: #0d9488; color: white; }
        .th-btn-teal:hover  { background: #0f766e; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(13,148,136,.3); }
        .th-btn-amber { background: #d97706; color: white; }
        .th-btn-amber:hover { background: #b45309; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(217,119,6,.3); }

        /* ── Info pill ────────────────────────────────────────── */
        .th-info {
            font-size: .78rem; color: #64748b;
            margin-top: .25rem;
            display: flex; align-items: center; gap: .3rem;
        }
        .dark .th-info { color: #94a3b8; }

        /* ── Info box ─────────────────────────────────────────── */
        .th-infobox-amber {
            margin-top: 1rem; padding: .75rem;
            border-radius: .5rem;
            background: #fffbeb; border: 1px solid #fde68a;
        }
        .dark .th-infobox-amber { background: rgba(69,26,3,.3); border-color: rgba(120,53,15,.3); }
        .th-infobox-teal {
            margin-top: 1rem; padding: .75rem;
            border-radius: .5rem;
            background: #f0fdfa; border: 1px solid #99f6e4;
        }
        .dark .th-infobox-teal  { background: rgba(19,78,74,.3); border-color: rgba(17,94,89,.3); }

        .th-infobox-title { font-size: .75rem; font-weight: 600; }
        .th-infobox-amber .th-infobox-title { color: #b45309; }
        .dark .th-infobox-amber .th-infobox-title { color: #fcd34d; }
        .th-infobox-teal .th-infobox-title  { color: #0f766e; }
        .dark .th-infobox-teal .th-infobox-title  { color: #5eead4; }
        .th-infobox-text { font-size: .75rem; margin-top: .25rem; }
        .th-infobox-amber .th-infobox-text { color: #92400e; }
        .dark .th-infobox-amber .th-infobox-text { color: #d97706; }
        .th-infobox-teal .th-infobox-text  { color: #065f46; }
        .dark .th-infobox-teal .th-infobox-text  { color: #0d9488; }

        /* ── Grid layout ──────────────────────────────────────── */
        .th-grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 1024px) {
            .th-grid-2 { grid-template-columns: 1fr 1fr; }
        }

        .th-flex-row   { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1rem; }
        .th-flex-input { display: flex; align-items: center; gap: .75rem; margin-top: 1rem; }
        .th-flex-grow  { flex: 1; }
        .th-mt-1       { margin-top: .25rem; }
        .th-mt-4       { margin-top: 1rem; }
        .th-mb-4       { margin-bottom: 1rem; }
        .th-mono       { font-family: ui-monospace, monospace; font-weight: 700; }
        .th-amber      { color: #d97706; }
        .th-teal       { color: #0d9488; }
        .th-muted      { color: #94a3b8; font-size: .85rem; }
        .dark .th-muted { color: #64748b; }
        .th-truncate   { max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .th-nowrap     { white-space: nowrap; }
        .th-overflow-x { overflow-x: auto; }
    </style>

    {{-- ══════════════════════════════════════════════════════════
         ROW 1 — DUA KARTU THRESHOLD SEJAJAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="th-grid-2">

        {{-- ── KARTU 1: FaceNet Recognition Threshold ──────────── --}}
        <div class="th-card">
            <div class="th-flex-row">
                <div>
                    <h3 class="th-section-title">
                        {{-- Icon: shield-check (20x20px, inline style agar tidak bergantung Tailwind) --}}
                        <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;color:#f59e0b"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        FaceNet Recognition Threshold
                    </h3>
                    <p class="th-info th-mt-1">
                        <svg style="width:.875rem;height:.875rem;flex-shrink:0"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Cosine similarity minimum untuk verifikasi identitas (FaceNet)
                    </p>
                </div>
                <span class="th-badge th-badge-amber" id="fn-badge">
                    {{ number_format($facenet_value, 2) }}
                </span>
            </div>

            {{-- Slider FaceNet --}}
            <div class="th-mb-4">
                <input
                    type="range"
                    min="0" max="1" step="0.01"
                    wire:model.live="facenet_value"
                    class="th-slider th-slider-amber"
                    style="--val: {{ $facenet_value * 100 }}%; color:#d97706;"
                    oninput="
                        this.style.setProperty('--val', (this.value*100)+'%');
                        document.getElementById('fn-input').value = parseFloat(this.value).toFixed(2);
                        document.getElementById('fn-badge').textContent = parseFloat(this.value).toFixed(2);
                    "
                    id="fn-slider"
                >
                <div style="display:flex;justify-content:space-between;margin-top:.375rem;">
                    <span class="trade-label">← Lebih Longgar (FAR ↑)</span>
                    <span class="trade-label">(FRR ↑) Lebih Ketat →</span>
                </div>
            </div>

            {{-- Input angka + catatan + tombol --}}
            <div class="th-flex-input">
                <input type="number" min="0" max="1" step="0.01"
                    wire:model.live="facenet_value"
                    id="fn-input"
                    class="th-input"
                    style="border-color:#fcd34d;color:#d97706;"
                    oninput="
                        let v=Math.min(1,Math.max(0,parseFloat(this.value)||0));
                        document.getElementById('fn-slider').value=v;
                        document.getElementById('fn-slider').style.setProperty('--val',(v*100)+'%');
                        document.getElementById('fn-badge').textContent=v.toFixed(2);
                    "
                >
                <div class="th-flex-grow">
                    <input type="text" wire:model="facenet_note"
                        placeholder="Catatan (opsional)..."
                        class="th-note-input"
                        style="border-color:#e2e8f0;"
                    >
                </div>
                <button wire:click="saveFacenet" wire:loading.attr="disabled" class="th-btn th-btn-amber">
                    <svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span wire:loading.remove wire:target="saveFacenet">Simpan</span>
                    <span wire:loading wire:target="saveFacenet">Menyimpan...</span>
                </button>
            </div>

            {{-- Info panduan --}}
            <div class="th-infobox-amber">
                <p class="th-infobox-title">💡 Panduan Nilai</p>
                <p class="th-infobox-text">
                    <strong>0.60–0.70</strong> = Longgar (kamera buruk) &nbsp;·&nbsp;
                    <strong>0.75</strong> = Standar &nbsp;·&nbsp;
                    <strong>0.80–0.90</strong> = Ketat (presisi tinggi)
                </p>
            </div>
        </div>

        {{-- ── KARTU 2: Liveness Detection Threshold ───────────── --}}
        <div class="th-card">
            <div class="th-flex-row">
                <div>
                    <h3 class="th-section-title">
                        <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;color:#14b8a6"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Liveness Threshold
                        <span style="font-size:.75rem;font-weight:400;color:#94a3b8;">(MiniFASNetV2-SE)</span>
                    </h3>
                    <p class="th-info th-mt-1">
                        <svg style="width:.875rem;height:.875rem;flex-shrink:0"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Score ≥ threshold → wajah REAL · Score &lt; threshold → SPOOF
                    </p>
                </div>
                <span class="th-badge th-badge-teal" id="lv-badge">
                    {{ number_format($liveness_value, 2) }}
                </span>
            </div>

            {{-- Slider Liveness --}}
            <div class="th-mb-4">
                <input
                    type="range"
                    min="0" max="1" step="0.01"
                    wire:model.live="liveness_value"
                    class="th-slider th-slider-teal"
                    style="--val: {{ $liveness_value * 100 }}%; color:#0d9488;"
                    oninput="
                        this.style.setProperty('--val', (this.value*100)+'%');
                        document.getElementById('lv-input').value = parseFloat(this.value).toFixed(2);
                        document.getElementById('lv-badge').textContent = parseFloat(this.value).toFixed(2);
                    "
                    id="lv-slider"
                >
                <div style="display:flex;justify-content:space-between;margin-top:.375rem;">
                    <span class="trade-label">← Lebih Longgar (APCER ↑)</span>
                    <span class="trade-label">(BPCER ↑) Lebih Ketat →</span>
                </div>
            </div>

            {{-- Input angka + catatan + tombol --}}
            <div class="th-flex-input">
                <input type="number" min="0" max="1" step="0.01"
                    wire:model.live="liveness_value"
                    id="lv-input"
                    class="th-input"
                    style="border-color:#99f6e4;color:#0d9488;"
                    oninput="
                        let v=Math.min(1,Math.max(0,parseFloat(this.value)||0));
                        document.getElementById('lv-slider').value=v;
                        document.getElementById('lv-slider').style.setProperty('--val',(v*100)+'%');
                        document.getElementById('lv-badge').textContent=v.toFixed(2);
                    "
                >
                <div class="th-flex-grow">
                    <input type="text" wire:model="liveness_note"
                        placeholder="Catatan (opsional)..."
                        class="th-note-input"
                        style="border-color:#e2e8f0;"
                    >
                </div>
                <button wire:click="saveLiveness" wire:loading.attr="disabled" class="th-btn th-btn-teal">
                    <svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span wire:loading.remove wire:target="saveLiveness">Simpan</span>
                    <span wire:loading wire:target="saveLiveness">Menyimpan...</span>
                </button>
            </div>

            {{-- Info panduan --}}
            <div class="th-infobox-teal">
                <p class="th-infobox-title">💡 Panduan Nilai</p>
                <p class="th-infobox-text">
                    <strong>0.30–0.45</strong> = Longgar (BPCER rendah) &nbsp;·&nbsp;
                    <strong>0.50</strong> = Standar &nbsp;·&nbsp;
                    <strong>0.60–0.80</strong> = Ketat (APCER rendah)
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         ROW 2 — RIWAYAT THRESHOLD SEJAJAR
    ══════════════════════════════════════════════════════════ --}}
    <div class="th-grid-2">

        {{-- Riwayat FaceNet --}}
        <div class="th-card">
            <h3 class="th-section-title th-mb-4">
                <svg style="width:1rem;height:1rem;flex-shrink:0;color:#f59e0b"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat FaceNet Threshold
            </h3>

            @if(count($facenetHistory) === 0)
                <p class="th-muted" style="text-align:center;padding:1.5rem 0;">Belum ada riwayat</p>
            @else
                <div class="th-overflow-x">
                    <table class="th-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Nilai</th>
                                <th>Catatan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facenetHistory as $row)
                            <tr>
                                <td class="th-nowrap">
                                    {{ \Carbon\Carbon::parse($row['created_at'])->format('d/m H:i') }}
                                </td>
                                <td>
                                    <span class="th-mono th-amber">
                                        {{ number_format($row['value'], 2) }}
                                    </span>
                                </td>
                                <td class="th-muted th-truncate">{{ $row['note'] ?? '-' }}</td>
                                <td>
                                    @if($row['is_active'])
                                        <span class="badge-active">Aktif</span>
                                    @else
                                        <span class="badge-inactive">Lama</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Riwayat Liveness --}}
        <div class="th-card">
            <h3 class="th-section-title th-mb-4">
                <svg style="width:1rem;height:1rem;flex-shrink:0;color:#14b8a6"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Riwayat Liveness Threshold
            </h3>

            @if(count($livenessHistory) === 0)
                <p class="th-muted" style="text-align:center;padding:1.5rem 0;">Belum ada riwayat</p>
            @else
                <div class="th-overflow-x">
                    <table class="th-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Nilai</th>
                                <th>Catatan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($livenessHistory as $row)
                            <tr>
                                <td class="th-nowrap">
                                    {{ \Carbon\Carbon::parse($row['created_at'])->format('d/m H:i') }}
                                </td>
                                <td>
                                    <span class="th-mono th-teal">
                                        {{ number_format($row['value'], 2) }}
                                    </span>
                                </td>
                                <td class="th-muted th-truncate">{{ $row['note'] ?? '-' }}</td>
                                <td>
                                    @if($row['is_active'])
                                        <span class="badge-active">Aktif</span>
                                    @else
                                        <span class="badge-inactive">Lama</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</x-filament-panels::page>
