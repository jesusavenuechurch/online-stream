<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('church_name')->nullable();
            $table->enum('type', ['pastor', 'member'])->default('member');
            $table->timestamps();
            
            // Indexes for faster lookups
            $table->index('username');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};