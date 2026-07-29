<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        if (! $this->patient) {
            return [];
        }

        return [
            'id'          => $this->patient->id,
            'name'        => $this->patient->fullName,
            'email'       => $this->patient->email,
            'age'         => $this->patient->age,
            'gender'      => ucfirst($this->patient->gender),
            'assigned_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
