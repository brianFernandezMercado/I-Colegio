<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use App\Models\SubServicio;

class SubCategoriaSeleccionadas extends Model
{
    protected $table = 'subcategorias_seleccionadas';

    protected $fillable = [
        'id',
        'user_id',
        'sub_servicio_id',
        'activo',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    function subServicio()
    {
        return $this->belongsTo(SubServicio::class, 'sub_servicio_id');
    }
}
