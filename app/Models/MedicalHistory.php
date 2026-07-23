<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    protected $fillable = [
        'patient_id',
        'caregiver_id',
        'condition_name',
        'diagnosis_date',
        'status',
        'severity',
        'notes',
        'diagnosed_by',
        'review_date'
    ];
}