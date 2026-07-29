<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medication extends Model
{
    protected $fillable = [
        'patient_id',
        'medicine_name',
        'purpose',
        'dosage',
        'frequency',
        'times',
        'start_date',
        'end_date',
        'instructions',
        'prescribing_doctor',
        'status',
    ];

    protected $casts = [
        'times' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
