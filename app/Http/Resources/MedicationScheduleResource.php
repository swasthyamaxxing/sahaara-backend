<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'taken_at' => $this->taken_at,
            'time_for_reminder' => $this->time_for_reminder,
            'medication' => new MedicationResource($this->whenLoaded('medication')),
        ];
    }
}