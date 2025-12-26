<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('price_before_discount', 10, 2);
            $table->enum('type', ['one_time', 'subscription']);
            $table->enum('status', ['active', 'inactive']);
            $table->boolean('is_active')->default(true);
            $table->integer('order');
            $table->string('whatsapp_number')->nullable();
            $table->integer('orders_count')->default(0);
            $table->foreignId('category_id')->nullable()->constrained('service_categories', 'id')->nullOnDelete()->onUpdate('cascade');
            $table->index('order');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_pages');
    }
};
