<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provincia extends Model
{
    use HasFactory;

    protected $table = 'provincias';

    protected $fillable = ['nombre', 'icono', 'departamento_id', 'activo'];
    protected $hidden = ['created_at', 'updated_at'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}
