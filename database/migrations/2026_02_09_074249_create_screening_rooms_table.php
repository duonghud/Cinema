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
        Schema::create('screening_rooms', function (Blueprint $table) {
            $table->id('roomID');
            $table->string('roomName');
            $table->integer('capacity');

            $table->foreignId('screenTypeID')->constrained('screen_types', 'screenTypeID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screening_rooms');
    }
};
