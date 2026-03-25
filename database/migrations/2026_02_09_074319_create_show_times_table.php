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
        Schema::create('show_times', function (Blueprint $table) {
            $table->id('showTimeID');
            $table->date('showDate');
            $table->time('startTime');
            $table->time('endTime');

            $table->foreignId('movieID')->constrained('movies', 'movieID');
            $table->foreignId('roomID')->constrained('screening_rooms', 'roomID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('show_times');
    }
};
