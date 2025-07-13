<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubServicio extends Model
{
    use HasFactory;

    protected $table = 'sub_servicios';

    protected $fillable = ['nombre', 'descripcion', 'icono', 'categoria_servicio_id', 'activo'];

    protected $hidden = ['created_at', 'updated_at'];

    public function categoria()
    {
        return $this->belongsTo(CategoriaServicio::class, 'categoria_servicio_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'subcategorias_seleccionadas', 'sub_servicio_id', 'user_id');
    }
}
