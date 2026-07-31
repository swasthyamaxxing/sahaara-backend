<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Builder;

use App\Models\User;
use App\Models\MedicalHistory;
use App\Models\MedicationSchedule;
use App\Models\Appointment;

class Medication extends Model
{
    protected $fillable = [
        'patient_id',
        'name',
        'description',
        'dosage',
        'is_active',
        'appointment_id',
        'medical_history_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(MedicationSchedule::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medicalHistory(): BelongsTo
    {
        return $this->belongsTo(MedicalHistory::class);
    }

    /**
     * Scope a query to only include active medications.
     */
    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
