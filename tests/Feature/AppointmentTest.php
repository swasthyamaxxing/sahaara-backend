<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_store_an_appointment_with_doctor_name(): void
    {
        $patient = User::factory()->create([
            'role' => 'patient',
            'age' => 35,
            'gender' => 'female',
        ]);

        $response = $this->postJson('/api/appointments/store', [
            'patient_id' => $patient->id,
            'institution_name' => 'City Hospital',
            'doctor_name' => 'Dr. Smith',
            'date_time' => '2026-07-23 10:30:00',
            'presenting_problem' => 'Headache',
            'prescription' => 'Rest and hydration',
            'notes' => 'Monitor symptoms',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.doctor_name', 'Dr. Smith');

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_name' => 'Dr. Smith',
        ]);
    }
}
