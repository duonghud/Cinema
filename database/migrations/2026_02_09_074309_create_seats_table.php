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
        Schema::create('seats', function (Blueprint $table) {
            $table->id('seatID');
            $table->string('rowSeat', 5);
            $table->string('colSeat', 5);

            $table->foreignId('roomID')->constrained('screening_rooms', 'roomID')->cascadeOnDelete();
            $table->foreignId('seatTypeID')->constrained('seat_types', 'seatTypeID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
