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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->unique();
            $table->string('password');
            $table->string('title')->unique();
            $table->longText('description');
            $table->text('accaptable_message')->nullable();
            $table->text('unaccaptable_message')->nullable();
            $table->json('location')->nullable();
            $table->string('phone_number')->nullable();
            $table->decimal('confirmation_price')->nullable();
            $table->boolean('confirmation_status')->nullable();
            $table->string('open_at')->nullable();
            $table->string('close_at')->nullable();
            $table->longText('url')->nullable();
            $table->text('image')->nullable();
            $table->text('logo')->nullable();

            $table->string('verification_code')->nullable();
            $table->boolean('email_verified')->default(0);
            $table->text('email_verification_token')->nullable();
            $table->boolean('active')->default(0);
            $table->enum('status', ['published', 'not_published', 'under_review'])->default('not_published');
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('order')->unique();
            $table->integer('number_of_reservations')->default(0);
            $table->boolean('is_signed')->default(0);
            $table->boolean('booking_status')->default(0);
            $table->string('account_type')->default("organization");
            $table->fullText(['title', 'description', 'email']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
