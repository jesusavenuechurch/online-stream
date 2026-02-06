<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avatar_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('poster_path'); // e.g., avatar-templates/christmas-2024.jpg
            $table->integer('frame_x')->default(0); // X position (pixels from left)
            $table->integer('frame_y')->default(0); // Y position (pixels from top)
            $table->integer('frame_size')->default(200); // Frame width/height
            $table->enum('frame_shape', ['circle', 'square'])->default('circle');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avatar_templates');
    }
};