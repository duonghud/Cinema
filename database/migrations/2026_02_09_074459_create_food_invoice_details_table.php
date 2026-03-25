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
        Schema::create('food_invoice_details', function (Blueprint $table) {
            $table->foreignId('foodInvoiceID')->constrained('food_invoices', 'foodInvoiceID')->cascadeOnDelete();
            $table->foreignId('foodID')->constrained('food', 'foodID');
            $table->integer('quantity');
            $table->primary(['foodInvoiceID', 'foodID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_invoice_details');
    }
};
