<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_caregiver_can_create_and_patient_can_view_active_medications(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
            'age' => 42,
            'gender' => 'female',
        ]);

        $response = $this->postJson('/api/medications/store', [
            'patient_id' => $patient->id,
            'medicine_name' => 'Paracetamol',
            'purpose' => 'Pain relief',
            'dosage' => '500mg',
            'frequency' => 'Twice daily',
            'times' => ['08:00', '20:00'],
            'start_date' => '2026-07-29',
            'end_date' => '2026-08-05',
            'instructions' => 'Take with food',
            'prescribing_doctor' => 'Dr. Singh',
            'status' => 'active',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.medicine_name', 'Paracetamol')
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('medications', [
            'patient_id' => $patient->id,
            'medicine_name' => 'Paracetamol',
            'status' => 'active',
        ]);

        $viewResponse = $this->getJson('/api/medications/' . $patient->id);

        $viewResponse->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonCount(1, 'data');
    }
}
