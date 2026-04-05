<?php

namespace App\Exports;

use App\Models\Schedule;
use App\Services\FeeService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduleExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected int $seasonId;
    protected FeeService $feeService;

    public function __construct(int $seasonId)
    {
        $this->seasonId = $seasonId;
        $this->feeService = app(FeeService::class);
    }

    public function collection()
    {
        return Schedule::with(['prayerType', 'user'])
            ->where('season_id', $this->seasonId)
            ->whereNotNull('user_id')
            ->join('prayer_types', 'schedules.prayer_type_id', '=', 'prayer_types.id')
            ->orderBy('schedules.date')
            ->orderBy('prayer_types.sort_order')
            ->select('schedules.*')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis Sholat',
            'Imam',
            'Fee (Rp)',
        ];
    }

    public function map($schedule): array
    {
        return [
            $schedule->date->format('d/m/Y'),
            $schedule->prayerType->name ?? '-',
            $schedule->user->name ?? '-',
            number_format($this->feeService->calculateScheduleFee($schedule), 0, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Jadwal Imam Ramadan';
    }
}
