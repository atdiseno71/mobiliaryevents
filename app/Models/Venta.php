<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Venta
 *
 * @property $id
 * @property $codigo_factura
 * @property $id_cliente
 * @property $id_comprador
 * @property $productos
 * @property $cantidad
 * @property $tipo_cantidad
 * @property $total
 * @property $fecha_venta
 *
 * @property Cliente $cliente
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Venta extends Model
{
    public $timestamps = false;

    static $rules = [
		'codigo_factura' => 'required',
		'id_cliente' => 'required',
		'id_comprador' => 'required',
		'total' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['codigo_factura','id_cliente','id_comprador','productos','total','fecha_venta'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cliente()
    {
        return $this->hasOne('App\Models\Cliente', 'id', 'id_cliente');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'id_comprador');
    }


}
