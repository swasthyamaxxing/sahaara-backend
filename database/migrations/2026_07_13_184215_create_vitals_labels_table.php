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
        Schema::create('vitals_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // name of the vital: ex Blood Pressure
            $table->string('vital_label');      // label of the vital: ex blood_pressure usually the slug of the name
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitals_labels');
    }
};
