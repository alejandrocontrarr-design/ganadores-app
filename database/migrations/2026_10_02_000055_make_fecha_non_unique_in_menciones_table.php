<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menciones', function (Blueprint $table) {
            try {
                // Intenta eliminar el índice único de forma segura
                $table->dropUnique(['fecha']);
            } catch (\Exception $e) {
                // Si el índice no existe, la excepción es atrapada y continúa el despliegue
            }
        });
    }

    public function down(): void
    {
        Schema::table('menciones', function (Blueprint $table) {
            $table->unique('fecha');
        });
    }
};