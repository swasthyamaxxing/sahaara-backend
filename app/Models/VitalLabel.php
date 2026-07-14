<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VitalLabel extends Model
{
    protected $fillable = [
        'name', 'vital_label',
    ];

    protected $table = "vitals_labels";
}
