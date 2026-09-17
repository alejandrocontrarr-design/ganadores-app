<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ganadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('edad')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('facebook_id')->nullable();
            $table->date('fecha_dinamica')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->string('programa')->nullable();
            $table->string('premio')->nullable();
            $table->string('patrocinador')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ganadores');
    }
};