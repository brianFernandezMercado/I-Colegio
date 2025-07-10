<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
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
