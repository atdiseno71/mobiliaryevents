<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Gasto
 *
 * @property $id
 * @property $descripcion
 * @property $valor
 * @property $id_gasto
 * @property $fecha_gasto
 *
 * @property TipoGasto $tipoGasto
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Gasto extends Model
{
    public $timestamps = false;
    static $rules = [
		'descripcion' => 'required',
		'valor' => 'required',
		'id_gasto' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['descripcion','valor','id_gasto','fecha_gasto'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tipoGasto()
    {
        return $this->hasOne('App\Models\TipoGasto', 'id', 'id_gasto');
    }


}
