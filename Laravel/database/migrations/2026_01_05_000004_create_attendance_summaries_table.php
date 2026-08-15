<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->unique()->constrained()->cascadeOnDelete();

            // Totals
            $table->unsignedInteger('total_present')->default(0);
            $table->unsignedInteger('total_sessions')->default(0); // total sessions held (all types)

            // Computed rate: total_present / total_sessions * 100
            $table->decimal('attendance_rate', 5, 2)->default(0.00); // 0.00 – 100.00

            // Streak: consecutive sessions attended (resets on any miss)
            $table->unsignedInteger('streak')->default(0);

            // Last time they showed up
            $table->date('last_attended_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_summaries');
    }
};
