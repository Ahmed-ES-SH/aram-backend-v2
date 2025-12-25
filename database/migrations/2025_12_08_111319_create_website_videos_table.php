<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_videos', function (Blueprint $table) {
            $table->id();
            $table->string('video_id');
            $table->text('video_image')->nullable();
            $table->text('video_url');
            $table->string('aspect_ratio')->default('16:9');
            $table->enum('video_type', ['youtube', 'file'])->default('youtube');
            $table->boolean('is_file')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_videos');
    }
};
