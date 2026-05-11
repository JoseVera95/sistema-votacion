<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();

            $table->string('grado');
            $table->string('cedula')->unique(); // 👈 SIN after
            $table->string('nombres_completos');
            $table->string('foto')->nullable();

            $table->unsignedInteger('merit_order')->default(1);
            $table->unsignedInteger('first_place_votes')->default(0);
            $table->unsignedInteger('points_total')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};