<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

      public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    protected $fillable = [
        'nombre_completo',
        'username',
        'email',
        'password',
        'celular',
        'descripcion',
        'calificacion',
        'imagen',
        'zona',
        'experiencia',
        'ci',
        'estado',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subServicios()
    {
        return $this->belongsToMany(SubServicio::class, 'subcategorias_seleccionadas', 'user_id', 'sub_servicio_id');
    }

    public function agenda()
    {
        return $this->hasMany(AgendaUsuario::class, 'user_id');
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaServicio::class, 'categoria_id'); // si guardas la categoría del usuario en users.categoria_id
    }
}
