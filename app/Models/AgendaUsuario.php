<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgendaUsuario extends Model
{
    use HasFactory;

    protected $table = 'agenda_usuarios';

    protected $fillable = [
        'user_id', 'fecha', 'hora_inicio', 'hora_fin', 'estado','activo'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
