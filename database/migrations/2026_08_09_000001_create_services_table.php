<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            
            $table->string('name'); // "Sunday Morning Service - Aug 10, 2026"
            $table->date('date'); // 2026-08-10
            $table->time('time')->nullable(); // 09:00:00
            $table->string('location')->nullable(); // "Main Sanctuary"
            $table->boolean('is_special_event')->default(false); // true for special events
            $table->string('service_category')->nullable(); // "Sunday Morning", "Youth Event", etc.
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('date');
            $table->index('is_special_event');
            $table->index('service_category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
