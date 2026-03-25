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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('ticketID');
            $table->decimal('price', 10, 2);
            $table->string('status');

            $table->foreignId('showTimeID')->constrained('show_times', 'showTimeID');
            $table->foreignId('seatID')->constrained('seats', 'seatID');

            $table->unique(['showTimeID', 'seatID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
