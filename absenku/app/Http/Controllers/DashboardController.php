<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceLog;
use App\Helpers\AttendanceHelper;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user  = Auth::user();
        $today = Carbon::today();
        $month = Carbon::now()->month;
        $year  = Carbon::now()->year;

        // ── Status & log hari ini ─────────────────────────────
        $todayLog = AttendanceLog::where('user_id', $user->id)
            ->whereDate('attempted_at', $today)
            ->where('result', 'accepted')
            ->latest('attempted_at')
            ->first();

        // ── Check-in terakhir ─────────────────────────────────
        $lastCheckin = AttendanceLog::where('user_id', $user->id)
            ->where('result', 'accepted')
            ->latest('attempted_at')
            ->first();

        // ── Statistik bulan ini ───────────────────────────────
        $monthLogs = AttendanceLog::where('user_id', $user->id)
            ->whereMonth('attempted_at', $month)
            ->whereYear('attempted_at', $year)
            ->where('result', 'accepted')
            ->get();

        $totalHadir     = $monthLogs->count();
        $totalTerlambat = $monthLogs->filter(function ($log) {
            return AttendanceHelper::isLate($log->attempted_at);
        })->count();

        // ── Aktivitas 7 hari terakhir untuk bar chart ─────────
        $weekActivity = collect(range(6, 0))->map(function ($daysAgo) use ($user) {
            $date = Carbon::today()->subDays($daysAgo);
            $log  = AttendanceLog::where('user_id', $user->id)
                ->whereDate('attempted_at', $date)
                ->where('result', 'accepted')
                ->first();
            return [
                'date'    => $date->format('Y-m-d'),
                'label'   => $date->locale('id')->isoFormat('dd')[0],
                'present' => $log ? true : false,
                'score'   => $log ? $log->confidence_score * 100 : 0,
            ];
        });

        // ── 3 log terkini untuk timeline ──────────────────────
        $recentLogs = AttendanceLog::where('user_id', $user->id)
            ->where('result', 'accepted')
            ->latest('attempted_at')
            ->take(3)
            ->get();

        // ── Riwayat lengkap dengan filter ──────────────────────
        $query = AttendanceLog::where('user_id', $user->id)
            ->latest('attempted_at');

        if (request('status')) {
            $query->where('result', request('status'));
        }

        if (request('date')) {
            $query->whereDate('attempted_at', request('date'));
        }

        $historyLogs = $query->paginate(6)->withQueryString();

        return view('dashboard', compact(
            'user',
            'todayLog',
            'lastCheckin',
            'totalHadir',
            'totalTerlambat',
            'weekActivity',
            'recentLogs',
            'historyLogs',
        ));
    }
}