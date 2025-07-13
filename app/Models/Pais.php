<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = ['id','nombre', 'icono', 'activo'];

    protected $hidden = ['created_at', 'updated_at'];

    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }
}
