<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->string('email')->nullable()->after('contact_number');
            $table->enum('gender', ['Male', 'Female'])->nullable()->after('email');
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Separated'])->nullable()->after('gender');
            $table->enum('membership_status', ['Member', 'Regular Attendee', 'Visitor', 'Inactive'])->default('Regular Attendee')->after('civil_status');
            $table->date('joined_date')->nullable()->after('membership_status');
            $table->date('date_of_baptism')->nullable()->after('joined_date');
            $table->text('notes')->nullable()->after('date_of_baptism');
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn(['email', 'gender', 'civil_status', 'membership_status', 'joined_date', 'date_of_baptism', 'notes']);
        });
    }
};
