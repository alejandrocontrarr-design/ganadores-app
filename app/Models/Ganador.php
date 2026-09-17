<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ganador extends Model
{
    use HasFactory;

    protected $table = 'ganadores';

    protected $fillable = [
        'nombre',
        'edad',
        'whatsapp',
        'facebook_id',
        'fecha_dinamica',
        'fecha_entrega',
        'programa',
        'premio',
        'patrocinador',
        'caza_premios',
    ];

    protected $casts = [
        'caza_premios'   => 'boolean',
        'fecha_dinamica' => 'date',
        'fecha_entrega'  => 'date',
    ];
}