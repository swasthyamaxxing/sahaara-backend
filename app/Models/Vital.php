<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Vital extends Model
{
    public function caretaker () {
        return $this->belongsTo(User::class, 'caretaker_id', 'id');
    }

    public function patient () {
        return $this->belongsTo(User::class, 'patient_id', 'id');
    }

    protected $fillable = [
        'caretaker_id', 'patient_id', 'vital_value', 'vital_label', 'vital_status', 'recorded_at'
    ];


    protected $table = "vitals";
}
