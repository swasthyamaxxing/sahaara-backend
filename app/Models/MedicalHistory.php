<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Builder;

class MedicalHistory extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'caretaker_id',
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

    public function caretaker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caretaker_id');
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






    /**
     * Filter records by search keyword.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('condition_name', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('action_taken', 'like', "%{$search}%");
            });
        });
    }

    /**
     * Filter records by status.
     */
    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn ($q) => $q->where('status', $status));
    }

    /**
     * Filter records by severity.
     */
    public function scopeWithSeverity(Builder $query, ?string $severity): Builder
    {
        return $query->when($severity, fn ($q) => $q->where('severity', $severity));
    }
}