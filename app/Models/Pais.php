<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = ['nombre', 'icono'];

    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }
}
