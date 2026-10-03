<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menciones', function (Blueprint $table) {
            // Verifica si el índice existe antes de intentar borrarlo
            if (Schema::hasIndex('menciones', 'menciones_fecha_unique')) {
                $table->dropUnique('menciones_fecha_unique');
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