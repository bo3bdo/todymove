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
        Schema::create('weekly_picks', function (Blueprint $table) {
            $table->id();
            $table->date('week_start_date');
            $table->foreignId('thursday_movie_id')->constrained('movies')->cascadeOnDelete();
            $table->foreignId('friday_movie_id')->constrained('movies')->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_picks');
    }
};
