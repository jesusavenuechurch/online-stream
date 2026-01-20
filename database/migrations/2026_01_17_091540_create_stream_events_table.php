<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stream_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('visibility', ['pastors_only', 'public'])->default('public');
            $table->enum('status', ['scheduled', 'live', 'ended'])->default('scheduled');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('recording_path')->nullable();
            $table->enum('recording_retention', ['until_next_stream', 'days', 'indefinite'])->default('until_next_stream');
            $table->integer('recording_retention_days')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('visibility');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stream_events');
    }
};