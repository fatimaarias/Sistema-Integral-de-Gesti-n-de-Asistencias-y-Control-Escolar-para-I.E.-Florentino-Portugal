<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';
    protected $fillable = ['nombre']; // ✅ solo nombre (A, B)

    // ❌ No belongsTo Grado
    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }

    public function users()
{
    return $this->belongsToMany(\App\Models\User::class);
}

}
