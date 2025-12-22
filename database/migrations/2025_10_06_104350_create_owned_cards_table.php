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
        Schema::create('owned_cards', function (Blueprint $table) {
            $table->id();
            $table->integer('cvv')->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->dateTime('issue_date')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->integer('current_usage')->default(0);
            $table->enum('owner_type', ['user', 'organization']);
            $table->string('card_number')->unique()->nullable();
            $table->enum('status', ['active', 'inactive', 'expired'])->default('inactive');
            $table->foreignId('card_id')->nullable()->constrained()->onDelete('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owned_cards');
    }
};
