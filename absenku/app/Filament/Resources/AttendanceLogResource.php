<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceLogResource\Pages;
use App\Models\AttendanceLog;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;

    // Gunakan tipe yang persis sama dengan parent trait HasNavigation
    protected static \UnitEnum|string|null $navigationGroup = 'Penelitian';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Log Absensi';
    protected static ?int    $navigationSort  = 2;
    protected static ?string $modelLabel      = 'Log Absensi';
    protected static ?string $pluralModelLabel = 'Log Absensi';

    // ── Read-only resource (tidak ada create/edit) ────────────
    public static function canCreate(): bool      { return false; }
    public static function canEdit($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(55),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->limit(20),

                TextColumn::make('attempted_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('result')
                    ->label('Hasil')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'accepted' => '✓ Diterima',
                        'rejected' => '✗ Ditolak',
                        default    => $state,
                    }),

                TextColumn::make('attack_type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'genuine'        => 'info',
                        'print_photo'    => 'danger',
                        'digital_screen' => 'warning',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'genuine'        => 'Genuine',
                        'print_photo'    => '⚠ Print Photo',
                        'digital_screen' => '⚠ Digital Screen',
                        default          => $state ?? '-',
                    }),

                TextColumn::make('evaluation_mode')
                    ->label('Sistem')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'full_pipeline' => 'success',
                        'facenet_only'  => 'warning',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'full_pipeline' => 'Sistem B',
                        'facenet_only'  => 'Sistem A',
                        default         => $state ?? '-',
                    }),

                TextColumn::make('confidence_score')
                    ->label('Confidence')
                    ->formatStateUsing(fn (float $state): string => number_format($state * 100, 1) . '%')
                    ->sortable(),

                TextColumn::make('liveness_score')
                    ->label('Liveness')
                    ->formatStateUsing(fn (?float $state): string => $state !== null
                        ? number_format($state * 100, 1) . '%'
                        : '–')
                    ->sortable(),

                TextColumn::make('rejection_reason')
                    ->label('Alasan Tolak')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'spoof_detected'   => 'danger',
                        'low_confidence'   => 'warning',
                        'no_face_detected' => 'gray',
                        'multiple_faces'   => 'warning',
                        default            => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'spoof_detected'   => '⚠ Spoof',
                        'low_confidence'   => 'Low Conf.',
                        'no_face_detected' => 'No Face',
                        'multiple_faces'   => 'Multi Face',
                        default            => $state ?? '–',
                    }),

                TextColumn::make('threshold_used')
                    ->label('Threshold')
                    ->formatStateUsing(fn (float $state): string => number_format($state, 2))
                    ->sortable(),

            ])

            // ── Filters ──────────────────────────────────────────────
            ->filters([

                SelectFilter::make('result')
                    ->label('Hasil')
                    ->options([
                        'accepted' => '✓ Diterima',
                        'rejected' => '✗ Ditolak',
                    ]),

                SelectFilter::make('attack_type')
                    ->label('Jenis Percobaan')
                    ->options([
                        'genuine'        => 'Genuine',
                        'print_photo'    => 'Print Photo',
                        'digital_screen' => 'Digital Screen',
                    ]),

                SelectFilter::make('evaluation_mode')
                    ->label('Mode Sistem')
                    ->options([
                        'full_pipeline' => 'Sistem B — Full Pipeline',
                        'facenet_only'  => 'Sistem A — FaceNet Only',
                    ]),

                SelectFilter::make('rejection_reason')
                    ->label('Alasan Tolak')
                    ->options([
                        'spoof_detected'   => 'Spoof Detected',
                        'low_confidence'   => 'Low Confidence',
                        'no_face_detected' => 'No Face',
                        'multiple_faces'   => 'Multiple Faces',
                    ]),

                Filter::make('date_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari')
                            ->native(false),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $q) => $q->whereDate('attempted_at', '>=', $data['dari'])
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $q) => $q->whereDate('attempted_at', '<=', $data['sampai'])
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null)   $indicators[] = 'Dari: ' . $data['dari'];
                        if ($data['sampai'] ?? null) $indicators[] = 'Sampai: ' . $data['sampai'];
                        return $indicators;
                    }),

            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)

            // ── Row Actions ───────────────────────────────────────────
            ->actions([

                Action::make('view_photo')
                    ->label('Foto')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->visible(fn (AttendanceLog $record): bool => (bool) $record->capture_path)
                    ->modalHeading(fn (AttendanceLog $record): string =>
                        'Foto Capture — ' . ($record->user?->name ?? 'Unknown') .
                        ' · ' . $record->attempted_at?->format('d/m/Y H:i:s')
                    )
                    ->modalContent(function (AttendanceLog $record) {
                        $imageData = null;
                        if ($record->capture_path && Storage::disk('local')->exists($record->capture_path)) {
                            $imageData = base64_encode(Storage::disk('local')->get($record->capture_path));
                        }
                        return view('filament.modals.capture-photo', [
                            'log'       => $record,
                            'imageData' => $imageData,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalWidth('lg'),

            ])

            // ── Bulk Actions ─────────────────────────────────────────
            ->bulkActions([

                BulkAction::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $filename = 'attendance_logs_' . now()->format('Ymd_His') . '.csv';

                        return response()->streamDownload(function () use ($records) {
                            $out = fopen('php://output', 'w');
                            fputs($out, "\xEF\xBB\xBF"); // BOM UTF-8 untuk Excel

                            fputcsv($out, [
                                'ID', 'User', 'Waktu', 'Hasil',
                                'Attack Type', 'Evaluation Mode',
                                'Confidence (%)', 'Liveness (%)',
                                'Rejection Reason', 'Spoof Type',
                                'Threshold FaceNet',
                            ]);

                            foreach ($records as $log) {
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

                DeleteBulkAction::make()
                    ->label('Hapus Terpilih'),

            ])

            ->defaultSort('attempted_at', 'desc')
            ->striped()
            ->paginated([25, 50, 100])
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceLogs::route('/'),
        ];
    }
}
