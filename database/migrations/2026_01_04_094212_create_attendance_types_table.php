<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Sunday Morning, Youth Service, etc.
            $table->boolean('is_recurring')->default(true);
            $table->string('day_of_week')->nullable(); // Sunday, Wednesday, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_types');
    }
};
