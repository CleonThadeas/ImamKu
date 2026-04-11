<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\RamadanSeason;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function index()
    {
        $seasons = RamadanSeason::latest()->get();
        $imams = User::where('role', 'imam')->orderBy('name')->get();
        return view('admin.exports.index', compact('seasons', 'imams'));
    }

    public function download(Request $request)
    {
        $type = $request->input('type');
        $seasonId = $request->input('season_id');

        if ($type === 'imams') {
            return $this->exportImams($request);
        } elseif ($type === 'schedules') {
            return $this->exportSchedules($request);
        } elseif ($type === 'attendances') {
            return $this->exportAttendances($request);
        }

        return redirect()->back()->with('error', 'Tipe export tidak ditemukan.');
    }

    private function exportImams(Request $request)
    {
        $query = User::where('role', 'imam');
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('is_active') && $request->is_active !== 'all') {
            $query->where('is_active', $request->is_active);
        }

        $imams = $query->get();

        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=daftar_imam_' . date('Ymd_His') . '.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $columns = ['ID', 'Nama', 'Email', 'Nomor Telepon', 'Nomor Rekening', 'Status Aktif', 'Terdaftar Pada'];

        $callback = function () use ($imams, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM to fix UTF-8 in Excel
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            fputcsv($file, $columns);
            foreach ($imams as $imam) {
                fputcsv($file, [
                    $imam->id,
                    $imam->name,
                    $imam->email,
                    $imam->phone_number ?? '-',
                    $imam->account_number ?? '-',
                    $imam->is_active ? 'Aktif' : 'Nonaktif',
                    $imam->created_at->format('Y-m-d H:i')
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function exportSchedules(Request $request)
    {
        $seasonId = $request->input('season_id');
        $query = Schedule::with(['user', 'prayerType', 'season']);
        
        if ($seasonId && $seasonId !== 'all') {
            $query->where('season_id', $seasonId);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('imam_id') && $request->imam_id !== 'all') {
            $query->where('user_id', $request->imam_id);
        }
        if ($request->filled('status_assign') && $request->status_assign !== 'all') {
            if ($request->status_assign === 'terisi') {
                $query->whereNotNull('user_id');
            } elseif ($request->status_assign === 'kosong') {
                $query->whereNull('user_id');
            }
        }

        $schedules = $query->orderBy('date')->get();

        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=jadwal_imam_' . date('Ymd_His') . '.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $columns = ['Season/Tahun', 'Tanggal', 'Waktu Sholat', 'Nama Imam', 'Status'];

        $callback = function () use ($schedules, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            fputcsv($file, $columns);
            foreach ($schedules as $schedule) {
                fputcsv($file, [
                    $schedule->season ? $schedule->season->name : '-',
                    $schedule->date ? $schedule->date->format('Y-m-d') : '-',
                    $schedule->prayerType ? $schedule->prayerType->name : '-',
                    $schedule->user ? $schedule->user->name : 'Kosong',
                    $schedule->user ? 'Terisi' : 'Belum Ditugaskan'
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function exportAttendances(Request $request)
    {
        $seasonId = $request->input('season_id');
        $query = Attendance::with(['schedule.user', 'schedule.prayerType', 'schedule.season']);
        
        $query->whereHas('schedule', function ($q) use ($seasonId, $request) {
            if ($seasonId && $seasonId !== 'all') {
                $q->where('season_id', $seasonId);
            }
            if ($request->filled('start_date')) {
                $q->whereDate('date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->whereDate('date', '<=', $request->end_date);
            }
            if ($request->filled('imam_id') && $request->imam_id !== 'all') {
                $q->where('user_id', $request->imam_id);
            }
        });

        if ($request->filled('attendance_status') && $request->attendance_status !== 'all') {
            $query->where('status', $request->attendance_status);
        }

        if ($request->filled('fee_status') && $request->fee_status !== 'all') {
            if ($request->fee_status === 'cair') {
                $query->where('status', 'approved');
            } elseif ($request->fee_status === 'batal') {
                $query->whereIn('status', ['rejected', 'expired']);
            } elseif ($request->fee_status === 'pending') {
                $query->where('status', 'pending');
            }
        }

        $attendances = $query->latest()->get();

        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=rekap_absensi_fee_' . date('Ymd_His') . '.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $columns = ['ID Transaksi', 'Season', 'Tanggal', 'Waktu Sholat', 'Imam Bertugas', 'Status Kehadiran', 'Waktu Pencatatan', 'Status Fee Keuangan', 'Nominal Pencairan (IDR)'];

        $callback = function () use ($attendances, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            
            fputcsv($file, $columns);
            
            $feeService = app(\App\Services\FeeService::class);
            
            foreach ($attendances as $att) {
                $sched = $att->schedule;
                $feeAmount = 0;
                $feeStatus = 'Pending / Belum Cair';
                
                if ($att->status === 'approved' && $sched) {
                    $feeAmount = $feeService->calculateScheduleFee($sched);
                    $feeStatus = 'Selesai (Cair)';
                } elseif ($att->status === 'rejected' || $att->status === 'expired') {
                    $feeStatus = 'Batal / Ditolak';
                }
                
                $feeAmountFormatted = number_format($feeAmount, 0, ',', '.');
                
                fputcsv($file, [
                    $att->id,
                    $sched && $sched->season ? $sched->season->name : '-',
                    $sched && $sched->date ? $sched->date->format('Y-m-d') : '-',
                    $sched && $sched->prayerType ? $sched->prayerType->name : '-',
                    $sched && $sched->user ? $sched->user->name : '-',
                    ucfirst($att->status),
                    $att->created_at ? $att->created_at->format('Y-m-d H:i:s') : '-',
                    $feeStatus,
                    $feeAmountFormatted
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
