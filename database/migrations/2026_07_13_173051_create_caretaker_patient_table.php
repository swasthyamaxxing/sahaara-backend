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
        Schema::create('caretaker_patient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caretaker_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');

            $table->unique(['caretaker_id', 'patient_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropUnique(['caretaker_id', 'patient_id']);
        $table->dropForeign(['caretaker_id']);
        $table->dropForeign(['patient_id']);
        Schema::dropIfExists('caretaker_patient');
    }
};
