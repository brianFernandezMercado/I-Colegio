<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = ['nombre', 'icono', 'pais_id', 'activo'];
    protected $hidden = ['created_at', 'updated_at'];
    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    public function provincias()
    {
        return $this->hasMany(Provincia::class);
    }
}
