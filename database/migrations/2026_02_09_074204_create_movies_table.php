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
        Schema::create('movies', function (Blueprint $table) {
            $table->id('movieID');
            $table->string('movieTitle');
            $table->text('description');
            $table->string('director');
            $table->string('poster')->nullable();
            $table->string('trailer')->nullable();
            $table->date('releaseDate');
            $table->integer('duration');
            $table->foreignId('ageRatingID')->constrained('age_ratings', 'ageRatingID');
            $table->foreignId('studioID')->constrained('studios', 'studioID');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
