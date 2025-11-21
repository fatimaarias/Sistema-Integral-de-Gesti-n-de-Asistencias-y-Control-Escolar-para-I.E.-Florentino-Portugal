<?php

namespace App\Imports;

use App\Models\Alumno;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AlumnosImport implements ToModel, WithHeadingRow
{
    protected $grado_id;
    protected $seccion_id;

    public function __construct($grado_id, $seccion_id)
    {
        $this->grado_id = $grado_id;
        $this->seccion_id = $seccion_id;
    }

    public function model(array $row)
    {
        return new Alumno([
            'nombre_apellido' => $row['nombre'],
            'direccion' => $row['direccion'] ?? null,
            'genero' => strtoupper($row['genero']) === 'M' ? 'MASCULINO' : 'FEMENINO',
            'grado_id' => $this->grado_id,
            'seccion_id' => $this->seccion_id,
        ]);
    }
}
