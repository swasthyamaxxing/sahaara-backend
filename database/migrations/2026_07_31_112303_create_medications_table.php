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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();

            // patient
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');

            // medicine
            $table->string('name', 255);
            $table->longText('description');
            $table->string('dosage')->nullable()->comment('example: 1 Tablet, 5 ml, etc.');
            
            // active status
            $table->boolean('is_active')->default(true);

            // relationships
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('medical_history_id')->nullable()->constrained('medical_histories')->onDelete('no action');

            $table->timestamps();
            
            $table->index(['patient_id', 'is_active']);    
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
