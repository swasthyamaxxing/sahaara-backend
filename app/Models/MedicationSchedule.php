<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Medication;

class MedicationSchedule extends Model
{
    protected $fillable = [
        'medication_id',
        'taken_at',
        'time_for_reminder',
    ];

    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}
