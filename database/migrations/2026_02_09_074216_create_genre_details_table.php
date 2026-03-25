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
        Schema::create('genre_details', function (Blueprint $table) {
            $table->foreignId('movieID')->constrained('movies', 'movieID')->cascadeOnDelete();
            $table->foreignId('genreID')->constrained('genres', 'genreID')->cascadeOnDelete();
            $table->primary(['movieID', 'genreID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_details');
    }
};
