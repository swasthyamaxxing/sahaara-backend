<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'caretaker_id',
        'patient_id',
        'institution_name',
        'doctor_name',
        'date_time',
        'presenting_problem',
        'prescription',
        'notes',
    ];

    protected $casts = [
        'date_time' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function caretaker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caretaker_id');
    }
}