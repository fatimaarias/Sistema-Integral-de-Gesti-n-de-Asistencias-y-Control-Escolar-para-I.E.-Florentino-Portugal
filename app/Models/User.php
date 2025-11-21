<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name','email','password','role'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function secciones()
{
    return $this->belongsToMany(\App\Models\Seccion::class);
}

public function hasRole(string $role): bool
{
    return $this->role === $role;
}

public function canManageSeccion(int $seccionId): bool
{
    if (in_array($this->role, ['admin','secundario'])) return true;
    return $this->secciones()->where('seccion_id', $seccionId)->exists();
}

}
