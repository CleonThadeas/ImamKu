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
        return view('admin.exports.index', compact('seasons'));
    }

    public function download(Request $request)
    {
        $type = $request->input('type');
        $seasonId = $request->input('season_id');

        if ($type === 'imams') {
            return $this->exportImams();
        } elseif ($type === 'schedules') {
            return $this->exportSchedules($seasonId);
        } elseif ($type === 'attendances') {
            return $this->exportAttendances($seasonId);
        }

        return redirect()->back()->with('error', 'Tipe export tidak ditemukan.');
    }

    private function exportImams()
    {
        $imams = User::where('role', 'imam')->get();

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

    private function exportSchedules($seasonId)
    {
        $query = Schedule::with(['user', 'prayerType', 'season']);
        if ($seasonId && $seasonId !== 'all') {
            $query->where('season_id', $seasonId);
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

    private function exportAttendances($seasonId)
    {
        $query = Attendance::with(['schedule.user', 'schedule.prayerType', 'schedule.season']);
        if ($seasonId && $seasonId !== 'all') {
            $query->whereHas('schedule', function ($q) use ($seasonId) {
                $q->where('season_id', $seasonId);
            });
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
