<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';

    protected $fillable = [
        'nit',
        'nombre',
        'email',
        'pagina_web',
        'pais',
        'region',
        'ciudad',
        'direccion',
        'telefonos',
        'logo',
    ];

    public $timestamps = true;
}
