<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    // ❌ Ya NO declaramos secciones()->hasMany()
    // Cada alumno apunta a grado_id, y la sección es global.
    
    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }
}
