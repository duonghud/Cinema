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
            $table->id('invoiceID');
            $table->dateTime('createDate');
            $table->decimal('totalAmount', 10, 2);

            $table->foreignId('customerID')->constrained('customers', 'customerID');
            $table->foreignId('adminID')->constrained('admins', 'adminID');
            $table->foreignId('paymentID')->constrained('payment_methods', 'paymentID');
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
