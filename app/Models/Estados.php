<?php

namespace App\Models;

use App\Traits\ProtectRelationships;
use Illuminate\Database\Eloquent\Model;

class Estados extends Model
{
    use ProtectRelationships;

    /**
     * Validation rules
     */
    public static $rules = [
        'nombre' => 'required|string|max:40',
        'slug'   => 'required|string|max:30',
    ];

    /**
     * Pagination size
     */
    protected $perPage = 20;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'nombre',
        'slug',
        'created_by',
    ];

    /**
     * Usuario que creó el registro
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
