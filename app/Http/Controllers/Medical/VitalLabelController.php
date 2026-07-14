<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\VitalLabel;

class VitalLabelController extends Controller
{
    public function index ( ) {
        $labels = VitalLabel::all();

        return response()->json([
            'status' => true,
            'data' => $labels
        ], 200);
    }
}
