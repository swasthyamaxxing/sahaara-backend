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
        Schema::create('medication_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('medication_id')->constrained('medications')->onDelete('cascade');

            $table->enum('taken_at', [
                'before_breakfast', 'after_breakfast',
                'before_lunch', 'after_lunch',
                'before_snacks', 'after_snacks',
                'before_dinner', 'after_dinner',
            ])->index();

            $table->time('time_for_reminder')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_schedules');
    }
};
