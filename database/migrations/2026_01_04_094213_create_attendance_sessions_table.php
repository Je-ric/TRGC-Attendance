<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_type_id')->constrained()->cascadeOnDelete();

            $table->date('date');

            // Optional: Morning, Afternoon, Day 1, etc.
            $table->string('service_name')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique combination: type + date + service name
            $table->unique(['attendance_type_id', 'date', 'service_name'], 'attendance_sessions_type_date_service_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
