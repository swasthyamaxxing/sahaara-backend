<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    protected $fillable = [
        'patient_id',
        'caregiver_id',
        'condition_name',
        'diagnosis_date',
        'end_date',
        'status',
        'severity',
        'notes',
        'action_taken',
        'diagnosed_by',
        'review_date',
    ];

    protected $casts = [
        'diagnosis_date' => 'date',
        'end_date' => 'date',
        'review_date' => 'date',
    ];

    // Adds these two calculated fields to every JSON response automatically
    protected $appends = ['duration_days', 'is_urgent'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }

    // Duration = end_date - diagnosis_date, or today - diagnosis_date if still ongoing
    public function getDurationDaysAttribute(): int
    {
        $end = $this->end_date ?? now();

        return (int) $this->diagnosis_date->diffInDays($end);
    }

    // Matches the frontend rule: Active + more than 7 days = urgent
    public function getIsUrgentAttribute(): bool
    {
        return $this->status === 'active' && $this->duration_days > 7;
    }
}