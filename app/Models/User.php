<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'name',
        'email',
        'password',
        'lastname',
        'phone',
        'image',
        'notification_token',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Accessor para la imagen que retorna la URL completa
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Si ya tiene /storage/ en la ruta (datos antiguos), usar como está
            if (strpos($this->image, '/storage/') === 0 || strpos($this->image, 'http') === 0) {
                return $this->image;
            }
            // Si no, agregar la URL base de storage
            return asset('storage/' . $this->image);
        }
        return null;
    }

    // Mutator para limpiar la ruta de imagen cuando se guarda
    public function setImageAttribute($value)
    {
        if ($value && strpos($value, '/storage/') === 0) {
            // Remover /storage/ si existe para guardar solo el path relativo
            $this->attributes['image'] = ltrim(str_replace('/storage/', '', $value), '/');
        } else {
            $this->attributes['image'] = $value;
        }
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_has_roles', 'user_id', 'role_id');
    }

    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }

}
