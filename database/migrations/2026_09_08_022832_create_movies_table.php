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
            $table->id();
            $table->string('tmdb_id')->nullable();
            $table->string('title');
            $table->string('director')->nullable();
            $table->string('poster_url')->nullable();
            $table->string('teaser_url')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->enum('status', ['now_showing', 'coming_soon', 'stopped'])->default('now_showing');
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
