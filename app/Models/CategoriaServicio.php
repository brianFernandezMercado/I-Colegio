<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaServicio extends Model
{
    use HasFactory;

    protected $table = 'categorias_servicios';

    protected $fillable = ['nombre', 'descripcion', 'icono'];

    public function subServicios()
    {
        return $this->hasMany(SubServicio::class, 'categoria_servicio_id');
    }
}
