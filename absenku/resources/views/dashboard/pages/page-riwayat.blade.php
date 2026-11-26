<div class="fa-page" id="fa-page-riwayat">

    <div class="fa-page-header">
        <h2>Riwayat Kehadiran</h2>
        <p>Rekap kehadiran lengkap dengan filter dan paginasi</p>
    </div>

    <div class="fa-history-filters">
        <input type="date" class="fa-filter-input" id="fa-filter-date" style="max-width:160px">
        <select class="fa-filter-select" id="fa-filter-status">
            <option value="">Semua Status</option>
            <option value="accepted">Hadir</option>
            <option value="rejected">Ditolak</option>
        </select>
        <button class="fa-filter-btn" onclick="faApplyFilter()">Filter</button>
        <button class="fa-filter-btn-reset" onclick="faResetFilter()">Reset</button>
    </div>

    <div class="fa-history-table-card">
        <table class="fa-htable">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Status</th>
                    <th>Skor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historyLogs as $log)
                    @php
                        $jam       = \Carbon\Carbon::parse($log->attempted_at)->format('H:i');
                        $terlambat = $log->result === 'accepted' && $jam > '09:00';
                        $status    = $log->result === 'accepted' ? ($terlambat ? 'terlambat' : 'hadir') : 'gagal';
                        $label     = $log->result === 'accepted' ? ($terlambat ? 'Terlambat' : 'Hadir') : 'Gagal';
                        $score     = round($log->confidence_score * 100, 1);
                        $barColor  = $score >= 95 ? 'var(--blue-600)' : ($score >= 85 ? 'var(--amber-600)' : '#E24B4A');
                    @endphp
                    <tr>
                        <td style="font-weight:500">
                            {{ \Carbon\Carbon::parse($log->attempted_at)->format('d M Y') }}
                        </td>
                        <td style="color:var(--gray-600)">
                            {{ \Carbon\Carbon::parse($log->attempted_at)->locale('id')->isoFormat('dddd') }}
                        </td>
                        <td style="font-weight:500">
                            {{ $jam }} <span style="font-size:11px;color:var(--gray-400)">WIB</span>
                        </td>
                        <td>
                            {{ $log->checked_out_at ? \Carbon\Carbon::parse($log->checked_out_at)->format('H:i') : '—' }}
                            @if($log->checked_out_at)
                                <span style="font-size:11px;color:var(--gray-400)">WIB</span>
                            @endif
                        </td>
                        <td>
                            <span class="fa-pill {{ $status }}">
                                <div class="fa-pill-dot"></div>{{ $label }}
                            </span>
                        </td>
                        <td>
                            <div class="fa-conf-mini">
                                <div class="fa-conf-mini-bar">
                                    <div class="fa-conf-mini-fill"
                                         style="width:{{ $score }}%;background:{{ $barColor }}"></div>
                                </div>
                                <span style="font-size:12px;font-weight:600;color:{{ $barColor }}">
                                    {{ $score }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:30px;color:var(--gray-400);font-size:13px">
                            Belum ada riwayat absensi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="fa-pagination">
            <div class="fa-pagination-info">
                Menampilkan {{ $historyLogs->firstItem() ?? 0 }}–{{ $historyLogs->lastItem() ?? 0 }}
                dari {{ $historyLogs->total() }} entri
            </div>
            <div class="fa-pagination-btns">
                @if($historyLogs->onFirstPage())
                    <button class="fa-pg-btn" disabled>‹</button>
                @else
                    <a href="{{ $historyLogs->previousPageUrl() }}#riwayat" class="fa-pg-btn">‹</a>
                @endif

                @foreach($historyLogs->getUrlRange(1, $historyLogs->lastPage()) as $page => $url)
                    <a href="{{ $url }}#riwayat"
                       class="fa-pg-btn {{ $page == $historyLogs->currentPage() ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($historyLogs->hasMorePages())
                    <a href="{{ $historyLogs->nextPageUrl() }}#riwayat" class="fa-pg-btn">›</a>
                @else
                    <button class="fa-pg-btn" disabled>›</button>
                @endif
            </div>
        </div>
    </div>
</div>