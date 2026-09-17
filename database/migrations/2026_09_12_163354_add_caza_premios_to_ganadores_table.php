<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ganadores', function (Blueprint $table) {
            $table->boolean('caza_premios')->default(false)->after('patrocinador');
        });
    }

    public function down(): void
    {
        Schema::table('ganadores', function (Blueprint $table) {
            $table->dropColumn('caza_premios');
        });
    }
};