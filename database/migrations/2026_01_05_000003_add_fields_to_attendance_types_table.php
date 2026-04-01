<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_types', function (Blueprint $table) {
            $table->string('description')->nullable()->after('day_of_week');
            $table->time('start_time')->nullable()->after('description');
            $table->string('location')->nullable()->after('start_time');
            $table->boolean('is_active')->default(true)->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_types', function (Blueprint $table) {
            $table->dropColumn(['description', 'start_time', 'location', 'is_active']);
        });
    }
};
