<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();

            // Model the patient and caretaker
            $table->foreignId('caretaker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');

            // Our core information
            $table->string('vital_label');
            $table->string('vital_value');

            // Mark each vital_value with a status of green, yellow, or red based on the readings
            $table->enum('vital_status', ['green', 'yellow', 'red']);

            // The day for which the vital is recorded (maybe they recorded vitals each day but posted it once a week)
            $table->timestamp('recorded_at');

            // Implement softDeletes: don't delete, but mark as deleted
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropForeign(['caretaker_id']);
        $table->dropForeign(['patient_id']);
        Schema::dropIfExists('vitals');
    }
};
