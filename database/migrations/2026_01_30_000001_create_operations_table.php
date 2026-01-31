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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->decimal('num1', 5, 2); // Máx 3 dígitos enteros + 2 decimales (ej: 999.99)
            $table->string('operator', 1); // +, -, *, /
            $table->decimal('num2', 5, 2); // Máx 3 dígitos enteros + 2 decimales
            $table->decimal('result', 8, 2)->nullable(); // Resultado puede ser mayor (999.99 * 999.99)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
