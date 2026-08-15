<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->string('contact_number')->nullable()->after('contact_person');
            $table->string('barangay')->nullable()->after('contact_number');
            $table->text('notes')->nullable()->after('barangay');
        });
    }

    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn(['contact_number', 'barangay', 'notes']);
        });
    }
};
