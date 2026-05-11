<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('rank');
            $table->unsignedInteger('points');
            $table->timestamps();

            $table->unique(['voter_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};