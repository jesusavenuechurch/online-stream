<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create zones table
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('order')->default(0); // For ordering in dropdown
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Create groups table
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->integer('order')->default(0); // For ordering in dropdown
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index('zone_id');
        });

        // Update attendees to use foreign keys
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropColumn(['zone', 'group']);
            $table->foreignId('zone_id')->nullable()->after('church_name')->constrained()->onDelete('set null');
            $table->foreignId('group_id')->nullable()->after('zone_id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['group_id']);
            $table->dropColumn(['zone_id', 'group_id']);
            $table->string('zone')->nullable();
            $table->string('group')->nullable();
        });
        
        Schema::dropIfExists('groups');
        Schema::dropIfExists('zones');
    }
};