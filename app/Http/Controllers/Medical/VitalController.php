<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Vital\StoreVitalRequest;

use App\Models\User;
use App\Models\Vital;

class VitalController extends Controller
{
    /**
     * Return the vitals of patients for the given timeframe
     * Anyone can be asking for the record: either the patient or the caretaker
     * However, we strictly request the patient's id: we are looking for that data
     */
    public function index(Request $request, User $patient)
    {

        // Who can see these results? The Caretaker and the Patient themselves

        // The user who is requesting the results
        $user = $request->user();

        // Either the user is the patient or the user is the caretaker of the patient
        if ($user->isPatient() && $user->id != $patient->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        } elseif ($user->isCaretaker() && $patient->caretaker->caretaker_id != $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to perform this action',
            ], 403);
        }

        $vitals = Vital::where('patient_id', $patient->id)
            ->latest('created_at')
            ->get()
            ->groupBy('vital_label');
        
        return response()->json([
            'status' => true,
            'data' => $vitals
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
    public function store(StoreVitalRequest $request)
    {
        // Validate the incoming request
        $request->validated();

        // User instance of the caretaker
        $user = $request->user();
        $patient_id = $request->input('patient_id');
        $recorded_at = $request->input('recorded_at');
        $vitals = $request->input('vitals');

        $failed = [];
        $success_count = 0;

        foreach ($vitals as $vital) {
            try {
                // Go over the vital records
                /**
                 * Verify that the vitals exist in database
                 * Find an appropriate status for the label: yellow, red, orange
                 * Create records in database one by one
                 */

                // We will work with the vital status a bit later

                $status = 'green';

                Vital::create([
                    'patient_id' => $patient_id,
                    'caretaker_id' => $user->id,

                    'vital_label' => $vital['label'],
                    'vital_value' => $vital['value'],

                    'vital_status' => $status,
                    'recorded_at' => $recorded_at,
                ]);

                $success_count++;
            } catch (\Exception $e) {
                $failed[] = $e;
            }
        }

        $total_vitals_count = count($vitals);

        if (count($failed) == 0) {
            return response()->json([
                'status' => true,
                'message' => 'All records updated successfully',
            ], 201);
        } elseif (count($failed) == $total_vitals_count) {
            return response()->json([
                'status' => false,
                'message' => 'No records could be updated',
                'failedRecords' => $failed
            ], 500);
        } else {
            return response()->json([
                'status' => true,
                'message' => "Successfully updated {$success_count} out of {$total_vitals_count} records",
                'failedRecords' => $failed
            ], 207);
        }
 
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
    public function edit(Vital $vital)
    {
        // Verify that the record is trying to be updated by the right person

        // Return the record
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vital $vital)
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
