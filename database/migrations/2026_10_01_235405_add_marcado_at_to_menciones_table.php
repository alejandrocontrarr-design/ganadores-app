<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menciones', function (Blueprint $table) {
            $table->timestamp('marcado_at')->nullable()->after('texto');
        });
    }

    public function down(): void
    {
        Schema::table('menciones', function (Blueprint $table) {
            $table->dropColumn('marcado_at');
        });
    }
};