<?php

namespace App\Filament\Resources\AttendanceLogResource\Pages;

use App\Filament\Resources\AttendanceLogResource;
use App\Models\AttendanceLog;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceLogs extends ListRecords
{
    protected static string $resource = AttendanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Export SEMUA log (tanpa harus select manual)
            Actions\Action::make('export_all')
                ->label('Export Semua (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $filename = 'attendance_logs_all_' . now()->format('Ymd_His') . '.csv';

                    return response()->streamDownload(function () {
                        $logs = AttendanceLog::with('user')
                            ->orderBy('attempted_at', 'desc')
                            ->cursor();   // efisien untuk data besar

                        $out = fopen('php://output', 'w');
                        fputs($out, "\xEF\xBB\xBF"); // BOM UTF-8 untuk Excel

                        fputcsv($out, [
                            'ID', 'User', 'Waktu', 'Hasil',
                            'Attack Type', 'Evaluation Mode',
                            'Confidence (%)', 'Liveness (%)',
                            'Rejection Reason', 'Spoof Type',
                            'Threshold FaceNet',
                        ]);

                        foreach ($logs as $log) {
                            fputcsv($out, [
                                $log->id,
                                $log->user?->name ?? 'Unknown',
                                $log->attempted_at?->format('Y-m-d H:i:s'),
                                $log->result,
                                $log->attack_type,
                                $log->evaluation_mode,
                                number_format($log->confidence_score * 100, 2),
                                $log->liveness_score !== null
                                    ? number_format($log->liveness_score * 100, 2)
                                    : '',
                                $log->rejection_reason ?? '',
                                $log->spoof_type ?? '',
                                number_format($log->threshold_used, 4),
                            ]);
                        }

                        fclose($out);
                    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                }),
        ];
    }
}
