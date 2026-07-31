<?php

namespace App\Http\Controllers\Medication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\MedicationScheduleResource;
use App\Models\User;
use App\Models\Medication;
use Illuminate\Http\JsonResponse;
use App\Models\MedicationSchedule;

class MedicationScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, User $patient): JsonResponse
    {
        // 1. Fetch active schedules with medication loaded
        $schedules = MedicationSchedule::with('medication')
            ->whereHas('medication', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id)
                    ->isActive();
            })
            ->orderBy('time_for_reminder', 'asc')
            ->get();

        // 2. Filter into buckets and transform with MedicationScheduleResource
        $groupedSchedules = [
            'morning' => MedicationScheduleResource::collection(
                $schedules->filter(fn ($s) => in_array($s->taken_at, [
                    'before_breakfast', 'after_breakfast'
                ]))->values()
            ),

            'afternoon' => MedicationScheduleResource::collection(
                $schedules->filter(fn ($s) => in_array($s->taken_at, [
                    'before_lunch', 'after_lunch', 'before_snacks', 'after_snacks'
                ]))->values()
            ),

            'evening' => MedicationScheduleResource::collection(
                $schedules->filter(fn ($s) => in_array($s->taken_at, [
                    'before_dinner', 'after_dinner'
                ]))->values()
            ),
        ];

        // 3. Return JSON response
        return response()->json([
            'status' => true,
            'message' => 'Retrieved daily medication schedule successfully',
            'data' => $groupedSchedules
        ], 200);
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
