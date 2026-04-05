<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'date'          => $this->date?->format('Y-m-d'),
            'date_formatted' => $this->date?->translatedFormat('l, d F Y'),
            'prayer_type'   => [
                'id'   => $this->prayerType?->id,
                'name' => $this->prayerType?->name,
            ],
            'prayer_time'   => $this->prayerTime?->effective_time,
            'imam'          => $this->whenLoaded('user', fn() => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
            'attendance'    => $this->whenLoaded('attendance', fn() => $this->attendance ? [
                'id'         => $this->attendance->id,
                'status'     => $this->attendance->status,
                'proof_url'  => $this->attendance->proof_path
                    ? asset('storage/' . $this->attendance->proof_path)
                    : null,
                'notes'      => $this->attendance->notes,
                'created_at' => $this->attendance->created_at?->toIso8601String(),
            ] : null),
            'can_check_in'  => $this->when(isset($this->can_check_in), $this->can_check_in ?? false),
            'can_swap'      => $this->when(isset($this->can_swap), $this->can_swap ?? false),
            'season_id'     => $this->season_id,
        ];
    }
}
