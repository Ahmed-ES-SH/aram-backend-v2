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



        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('invoice_number')->unique();
            $table->decimal('total_invoice', 10, 2);
            $table->decimal('before_discount', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('ref_code')->nullable();
            $table->enum('invoice_type', ["cards", "book", "service"]);
            $table->unsignedBigInteger('owner_id');
            $table->enum('owner_type', ['user', 'organization']);
            $table->enum('status', ['pending', 'canceled', 'paid'])->default('pending');
            $table->string('currency', 3)->default('OMR');
            $table->enum('payment_method', ['thawani', 'credit_card', 'wallet', 'cash'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
