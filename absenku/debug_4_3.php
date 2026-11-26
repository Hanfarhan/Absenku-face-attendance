<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AttendanceLog;

$mode = 'facenet_only';

$genuine_total    = AttendanceLog::byMode($mode)->genuine()->count();
$genuine_accepted = AttendanceLog::byMode($mode)->genuine()->accepted()->count();  // TA
$genuine_rejected = AttendanceLog::byMode($mode)->genuine()->rejected()->count();  // FR

$impostor_total    = AttendanceLog::byMode($mode)->attack()->count();
$impostor_accepted = AttendanceLog::byMode($mode)->attack()->accepted()->count();  // FA
$impostor_rejected = AttendanceLog::byMode($mode)->attack()->rejected()->count();  // TR

$tar = $genuine_total > 0 ? round($genuine_accepted / $genuine_total * 100, 2) : 0;
$frr = $genuine_total > 0 ? round($genuine_rejected / $genuine_total * 100, 2) : 0;
$far = $impostor_total > 0 ? round($impostor_accepted / $impostor_total * 100, 2) : 0;

$avg_conf = AttendanceLog::byMode($mode)->genuine()->avg('confidence_score');

echo "=== SISTEM A (FaceNet Only) ===\n";
echo "Genuine Attempts:\n";
echo "  Total       : $genuine_total\n";
echo "  Accepted(TA): $genuine_accepted\n";
echo "  Rejected(FR): $genuine_rejected\n";
echo "  TAR         : $tar%\n";
echo "  FRR         : $frr%\n";
echo "\nImpostor Attempts (genuine wajah orang lain):\n";
echo "  Total       : $impostor_total\n";
echo "  Accepted(FA): $impostor_accepted  ← ini yang buat FAR\n";
echo "  Rejected(TR): $impostor_rejected\n";
echo "  FAR         : $far%\n";
echo "\nAvg Confidence Score (genuine): " . round($avg_conf * 100, 2) . "%\n";
