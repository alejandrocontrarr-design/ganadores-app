<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mencion extends Model
{
    use HasFactory;

    protected $table = 'menciones';

    protected $fillable = [
        'fecha',
        'locutor',
        'texto',
        'marcado_at',
    ];
}