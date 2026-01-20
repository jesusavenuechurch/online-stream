<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stream_settings', function (Blueprint $table) {
            $table->id();
            $table->text('stream_key'); // Will be encrypted
            $table->string('rtmp_url')->default('rtmp://your-server.com/live');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stream_settings');
    }
};