<div class="fa-page active" id="fa-page-dashboard">

    {{-- Greeting --}}
    <div class="fa-greeting">
        <div class="fa-today-tag" id="fa-today-tag"></div>
        <h2 id="fa-greeting-text">Selamat Pagi, {{ auth()->user()->name }}!</h2>
        <p>Semoga harimu produktif. Jangan lupa absen hari ini.</p>
    </div>

    {{-- Stat cards --}}
    <div class="fa-stats-grid">

        {{-- Status hari ini --}}
        <div class="fa-stat-card">
            <div class="fa-stat-icon blue">
                <svg viewBox="0 0 24 24" fill="var(--blue-600)">
                    <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
                </svg>
            </div>
            <div class="fa-stat-label">Status Hari Ini</div>
            @if($todayLog)
                @php
                    $jamCheckin = \Carbon\Carbon::parse($todayLog->attempted_at)->format('H:i');
                    $terlambat  = $jamCheckin > '09:00';
                @endphp
                <div class="fa-stat-value" style="font-size:15px;color:var(--green-600)">Hadir</div>
                <div style="margin-top:4px">
                    <span class="fa-stat-badge {{ $terlambat ? 'amber' : 'green' }}">
                        {{ $terlambat ? 'Terlambat' : 'Tepat Waktu' }}
                    </span>
                </div>
            @else
                <div class="fa-stat-value" style="font-size:15px;color:var(--amber-600)">Belum Absen</div>
                <div style="margin-top:4px"><span class="fa-stat-badge amber">Segera Absen</span></div>
            @endif
        </div>

        {{-- Check-in terakhir --}}
        <div class="fa-stat-card">
            <div class="fa-stat-icon green">
                <svg viewBox="0 0 24 24" fill="var(--green-600)">
                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>
                </svg>
            </div>
            <div class="fa-stat-label">Check-In Terakhir</div>
            @if($lastCheckin)
                <div class="fa-stat-value">
                    {{ \Carbon\Carbon::parse($lastCheckin->attempted_at)->format('H:i') }}
                </div>
                <div class="fa-stat-sub">
                    {{ \Carbon\Carbon::parse($lastCheckin->attempted_at)->locale('id')->isoFormat('dddd, D MMM YYYY') }}
                </div>
            @else
                <div class="fa-stat-value" style="font-size:15px">—</div>
                <div class="fa-stat-sub">Belum ada data</div>
            @endif
        </div>

        {{-- Kehadiran bulan ini --}}
        <div class="fa-stat-card">
            <div class="fa-stat-icon amber">
                <svg viewBox="0 0 24 24" fill="var(--amber-600)">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/>
                </svg>
            </div>
            <div class="fa-stat-label">Kehadiran Bulan Ini</div>
            <div class="fa-stat-value">{{ $totalHadir }}</div>
            <div class="fa-stat-sub">
                {{ $totalTerlambat }} terlambat
                @if($totalHadir > 0)
                    <span class="fa-stat-badge amber" style="font-size:9px">
                        {{ number_format(($totalHadir / max(\Carbon\Carbon::now()->day, 1)) * 100, 1) }}%
                    </span>
                @endif
            </div>
        </div>

    </div>

    {{-- Charts row --}}
    <div class="fa-dash-row">

        <div class="fa-card">
            <div class="fa-card-header">
                <div class="fa-card-title">Aktivitas Minggu Ini</div>
            </div>
            <div class="fa-week-bars" id="fa-week-bars"></div>
            <div style="display:flex;justify-content:space-between;margin-top:4px" id="fa-week-labels"></div>
        </div>

        <div class="fa-card">
            <div class="fa-card-header">
                <div class="fa-card-title">Ringkasan Bulan Ini</div>
            </div>
            @php
                $totalHari  = \Carbon\Carbon::now()->day;
                $pct        = $totalHari > 0 ? round(($totalHadir / $totalHari) * 100) : 0;
                $circumference = 2 * pi() * 32;
                $dashHadir  = ($pct / 100) * $circumference;
                $dashSisa   = $circumference - $dashHadir;
                $dashLambat = $totalHadir > 0 ? (($totalTerlambat / $totalHadir) * $dashHadir) : 0;
            @endphp
            <div class="fa-donut-wrap">
                <svg width="90" height="90" viewBox="0 0 90 90">
                    <circle cx="45" cy="45" r="32" fill="none" stroke="#e2e8f0" stroke-width="12"/>
                    <circle cx="45" cy="45" r="32" fill="none" stroke="var(--blue-600)" stroke-width="12"
                            stroke-dasharray="{{ round($dashHadir, 2) }} {{ round($dashSisa, 2) }}"
                            stroke-dashoffset="8" stroke-linecap="round"/>
                    <text x="45" y="41" text-anchor="middle" font-size="15" font-weight="700"
                          fill="#1a2332" font-family="'Plus Jakarta Sans',sans-serif">{{ $pct }}%</text>
                    <text x="45" y="54" text-anchor="middle" font-size="9"
                          fill="#888780" font-family="'Plus Jakarta Sans',sans-serif">Kehadiran</text>
                </svg>
                <div class="fa-donut-legends">
                    <div class="fa-legend-item">
                        <div class="fa-legend-dot" style="background:var(--blue-600)"></div>
                        Hadir ({{ $totalHadir }} hari)
                    </div>
                    <div class="fa-legend-item">
                        <div class="fa-legend-dot" style="background:#FAC775"></div>
                        Terlambat ({{ $totalTerlambat }} hari)
                    </div>
                    <div class="fa-legend-item">
                        <div class="fa-legend-dot" style="background:#e2e8f0"></div>
                        Tidak Hadir ({{ max($totalHari - $totalHadir, 0) }})
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent timeline --}}
    <div class="fa-mt-14">
        <div class="fa-card">
            <div class="fa-card-header">
                <div class="fa-card-title">Riwayat Terkini</div>
                <button class="fa-card-action" onclick="faShowPage('riwayat', null)">Lihat Semua</button>
            </div>
            <div class="fa-timeline">
                @forelse($recentLogs as $index => $log)
                    @php
                        $jam       = \Carbon\Carbon::parse($log->attempted_at)->format('H:i');
                        $terlambat = $jam > '09:00';
                        $dotColor  = $terlambat ? 'amber' : 'blue';
                        $status    = $terlambat ? 'terlambat' : 'hadir';
                        $label     = $terlambat ? 'Terlambat' : 'Hadir';
                        $tanggal   = \Carbon\Carbon::parse($log->attempted_at)->locale('id')->isoFormat('dddd, D MMM');
                    @endphp
                    <div class="fa-tl-item">
                        <div class="fa-tl-dot-col">
                            <div class="fa-tl-dot {{ $dotColor }}"></div>
                            @if(!$loop->last)
                                <div class="fa-tl-line"></div>
                            @endif
                        </div>
                        <div class="fa-tl-content">
                            <div class="fa-tl-title">Check In — {{ $tanggal }}</div>
                            <div class="fa-tl-time">
                                {{ $jam }} · Skor: {{ number_format($log->confidence_score * 100, 1) }}%
                            </div>
                        </div>
                        <span class="fa-pill {{ $status }}">
                            <div class="fa-pill-dot"></div>{{ $label }}
                        </span>
                    </div>
                @empty
                    <div style="text-align:center;padding:20px;color:var(--gray-400);font-size:13px">
                        Belum ada riwayat absensi
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>