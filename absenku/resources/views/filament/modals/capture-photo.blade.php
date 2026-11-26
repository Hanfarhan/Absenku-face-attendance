{{-- Modal: tampilkan foto capture dari local disk --}}
<div style="text-align:center;padding:1rem 0;">

    @if($imageData)
        {{-- Info badge --}}
        <div style="display:flex;align-items:center;justify-content:center;gap:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
            <span style="background:{{ $log->result === 'accepted' ? '#dcfce7' : '#fee2e2' }};
                          color:{{ $log->result === 'accepted' ? '#166534' : '#991b1b' }};
                          padding:.25rem .75rem;border-radius:99px;font-size:.75rem;font-weight:700;">
                {{ $log->result === 'accepted' ? '✓ Diterima' : '✗ Ditolak' }}
            </span>

            @php
                $attackColors = [
                    'genuine'        => ['bg' => '#e0f2fe', 'text' => '#0369a1', 'label' => 'Genuine'],
                    'print_photo'    => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => '⚠ Print Photo'],
                    'digital_screen' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => '⚠ Digital Screen'],
                ];
                $ac = $attackColors[$log->attack_type] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'label' => $log->attack_type];
            @endphp
            <span style="background:{{ $ac['bg'] }};color:{{ $ac['text'] }};
                          padding:.25rem .75rem;border-radius:99px;font-size:.75rem;font-weight:700;">
                {{ $ac['label'] }}
            </span>

            <span style="background:{{ $log->evaluation_mode === 'full_pipeline' ? '#dcfce7' : '#fef3c7' }};
                          color:{{ $log->evaluation_mode === 'full_pipeline' ? '#166534' : '#92400e' }};
                          padding:.25rem .75rem;border-radius:99px;font-size:.75rem;font-weight:600;">
                {{ $log->evaluation_mode === 'full_pipeline' ? 'Sistem B' : 'Sistem A' }}
            </span>
        </div>

        {{-- Foto capture --}}
        <img src="data:image/jpeg;base64,{{ $imageData }}"
             alt="Capture {{ $log->user?->name }}"
             style="max-width:100%;max-height:400px;border-radius:.75rem;
                    border:2px solid #e2e8f0;object-fit:contain;margin:0 auto;display:block;">

        {{-- Detail metrik --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-top:1.25rem;text-align:left;">
            <div style="background:#f8fafc;border-radius:.5rem;padding:.75rem;">
                <div style="font-size:.65rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Confidence</div>
                <div style="font-size:1.1rem;font-weight:800;color:#0369a1;">{{ number_format($log->confidence_score * 100, 1) }}%</div>
            </div>
            <div style="background:#f8fafc;border-radius:.5rem;padding:.75rem;">
                <div style="font-size:.65rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Liveness</div>
                <div style="font-size:1.1rem;font-weight:800;color:#0d9488;">
                    {{ $log->liveness_score !== null ? number_format($log->liveness_score * 100, 1).'%' : '–' }}
                </div>
            </div>
            <div style="background:#f8fafc;border-radius:.5rem;padding:.75rem;">
                <div style="font-size:.65rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Threshold</div>
                <div style="font-size:1.1rem;font-weight:800;color:#7c3aed;">{{ number_format($log->threshold_used, 2) }}</div>
            </div>
        </div>

        @if($log->rejection_reason)
        <div style="margin-top:.75rem;padding:.5rem .875rem;background:#fff1f2;border-radius:.5rem;
                    font-size:.78rem;color:#991b1b;text-align:left;">
            <strong>Alasan tolak:</strong> {{ $log->rejection_reason }}
        </div>
        @endif

    @else
        {{-- Foto tidak tersedia --}}
        <div style="padding:3rem;color:#94a3b8;">
            <svg style="width:3rem;height:3rem;margin:0 auto 1rem;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p style="font-weight:600;color:#64748b;">Foto tidak tersedia</p>
            <p style="font-size:.8rem;margin-top:.25rem;">File mungkin sudah dihapus atau belum disimpan.</p>
        </div>
    @endif

</div>
