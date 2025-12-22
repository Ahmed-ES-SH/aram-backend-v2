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
            $table->string('gmail_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->string('facebook_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->string('x_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->string('youtube_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->string('instgram_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->string('snapchat_account')->nullable();  // حساب البريد الإلكتروني للشركة
            $table->string('tiktok_account')->nullable();  // حساب البريد الإلكتروني للشركة
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
