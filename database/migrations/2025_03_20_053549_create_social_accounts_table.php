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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->text('whatsapp_number')->nullable();  // رقم واتساب الشركة
            $table->string('official_account')->nullable();
            $table->boolean('official_state')->default(false);
            $table->string('gmail_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->boolean('gmail_state')->default(false);
            $table->string('facebook_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->boolean('facebook_state')->default(false);
            $table->string('x_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->boolean('x_state')->default(false);
            $table->string('youtube_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->boolean('youtube_state')->default(false);
            $table->string('instgram_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->boolean('instgram_state')->default(false);
            $table->string('snapchat_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->boolean('snapchat_state')->default(false);
            $table->string('tiktok_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->boolean('tiktok_state')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
