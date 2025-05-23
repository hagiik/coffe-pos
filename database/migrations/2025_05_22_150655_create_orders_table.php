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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->enum('status', ['Menunggu','Diproses','Selesai'])->default('Menunggu');
            $table->enum('pembayaran', ['Menunggu','Sudah Dibayar'])->default('Menunggu');
            $table->enum('order_type', ['ditempat', 'pulang'])->default('ditempat');
            $table->enum('payment_method', ['Cash', 'Qris'])->nullable();
            $table->decimal('total_price', 10, 2)->default(0); // hasil sum dari order_items
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
