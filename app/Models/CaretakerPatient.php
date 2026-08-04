<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaretakerPatient extends Model
{

    public function patient() {
        return $this->belongsTo(User::class, 'patient_id', 'id');
    }

    public function caretaker() {
        return $this->belongsTo(User::class, 'caretaker_id', 'id');
    }

    protected $fillable = [
        'caretaker_id', 'patient_id',
    ];

    protected $table = "caretaker_patient";
    
}
