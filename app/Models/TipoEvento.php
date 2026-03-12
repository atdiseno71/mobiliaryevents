<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{

    /**
     * Validation rules
     */
    public static $rules = [
        'nombre' => 'required|string|max:255',
    ];

    /**
     * Pagination
     */
    protected $perPage = 20;

    /**
     * Fillable fields
     */
    protected $fillable = [
        'nombre',
        'created_by',
    ];

    /**
     * Autor/creador del registro
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
