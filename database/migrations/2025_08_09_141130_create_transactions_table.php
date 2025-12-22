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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('account_type', ['user', 'organization']);
            $table->enum('type', [
                'deposit',
                'withdrawal',
                'purchase',
                'sale',
                'commission',
                'refund',
                'transfer',
                'book'
            ]);
            $table->decimal('amount', 10, 2); // always positive
            $table->enum('direction', ['in', 'out']); // in = رصيد دخل، out = خرج
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->string('source_type')->nullable(); // service, order, withdrawal, etc
            $table->unsignedBigInteger('source_id')->nullable(); // ID of the related entity
            $table->text('note')->nullable(); // optional description
            $table->json('meta')->nullable(); // for extra data (e.g. payment method, gateway ref)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
