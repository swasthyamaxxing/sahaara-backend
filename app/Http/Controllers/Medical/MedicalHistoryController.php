<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\MedicalHistory;
use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    public function index(Request $request, user $patient)
    {
        $medicalHistories = MedicalHistory::where('patient_id', $patient)
            ->get();

        return response()->json($medicalHistories);
    }
}