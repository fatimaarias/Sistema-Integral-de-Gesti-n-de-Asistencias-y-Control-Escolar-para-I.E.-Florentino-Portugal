<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $fillable = ['nombre_apellido', 'direccion', 'genero', 'grado_id', 'seccion_id'];

    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
