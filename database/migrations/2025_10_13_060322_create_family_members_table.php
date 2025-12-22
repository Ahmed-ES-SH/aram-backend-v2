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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            // The user who owns this family record
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // The related family member (also a user)
            $table->foreignId('family_member_id')->constrained('users')->onDelete('cascade');

            // Optional: relationship type (e.g., father, mother, brother)
            $table->string('relationship')->nullable();

            // Optional: status (pending, accepted, rejected)
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');

            $table->timestamps();

            // Prevent duplicates (same user cannot add the same member twice)
            $table->unique(['user_id', 'family_member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
