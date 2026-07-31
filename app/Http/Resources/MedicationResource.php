<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'dosage' => $this->dosage,
            'is_active' => (bool) $this->is_active,
            'appointment_id' => $this->appointment_id,
            'medical_history_id' => $this->medical_history_id,
            'schedules' => MedicationScheduleResource::collection($this->whenLoaded('schedules')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}