<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;

use App\Models\VitalLabel;

class VitalsLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vitals = [
            'Blood Pressure Systolic',
            'Blood Pressure Diastolic',
            'Blood Sugar',
            'Heartbeat',
            'Body Weight',
            'Oxygen Saturation',
            'Body Temperature'
        ];

        foreach ($vitals as $vital) {
            VitalLabel::create([
                'name' => $vital,
                'vital_label' => Str::slug($vital),
            ]);
        }
    }
}
