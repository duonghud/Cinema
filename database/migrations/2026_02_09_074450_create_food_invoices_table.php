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
        Schema::create('food_invoices', function (Blueprint $table) {
            $table->id('foodInvoiceID');
            $table->dateTime('orderDate');
            $table->decimal('total', 10, 2);
            $table->foreignId('customerID')->constrained('customers', 'customerID');
            $table->foreignId('adminID')->constrained('admins', 'adminID');
            $table->foreignId('paymentID')->constrained('payment_methods', 'paymentID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_invoices');
    }
};
