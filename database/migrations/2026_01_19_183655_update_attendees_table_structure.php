<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            // Drop old name field
            $table->dropColumn('name');
            
            // Add new fields for structured names
            $table->string('title')->nullable()->after('id'); // Mr, Mrs, Dr, Pastor, etc.
            $table->string('first_name')->after('title');
            $table->string('last_name')->after('first_name');
            
            // Add zone and group
            $table->string('zone')->nullable()->after('church_name');
            $table->string('group')->nullable()->after('zone');
            
            // Update phone to be required (remove nullable if needed later)
            // Email is already nullable which is fine
        });
    }

    public function down(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropColumn(['title', 'first_name', 'last_name', 'zone', 'group']);
            $table->string('name')->after('id');
        });
    }
};