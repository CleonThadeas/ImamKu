<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SwapResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'status' => $this->status,
            'requester' => [
                'id'   => $this->requester?->id,
                'name' => $this->requester?->name,
            ],
            'schedule' => $this->whenLoaded('schedule', fn() => [
                'id'          => $this->schedule->id,
                'date'        => $this->schedule->date?->format('Y-m-d'),
                'prayer_type' => $this->schedule->prayerType?->name,
                'imam_name'   => $this->schedule->user?->name,
            ]),
            'target_schedule' => $this->whenLoaded('targetSchedule', fn() => $this->targetSchedule ? [
                'id'          => $this->targetSchedule->id,
                'date'        => $this->targetSchedule->date?->format('Y-m-d'),
                'prayer_type' => $this->targetSchedule->prayerType?->name,
                'imam_name'   => $this->targetSchedule->user?->name,
            ] : null),
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
