<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menciones', function (Blueprint $table) {
            // Elimina el índice único si existía sobre el campo fecha
            $table->dropUnique(['fecha']); 
        });
    }

    public function down(): void
    {
        Schema::table('menciones', function (Blueprint $table) {
            $table->unique('fecha');
        });
    }
};