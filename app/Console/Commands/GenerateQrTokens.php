<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alumno;
use Illuminate\Support\Str;

class GenerateQrTokens extends Command
{
    protected $signature = 'alumnos:qr-tokens';
    protected $description = 'Genera tokens QR únicos para alumnos sin token';

    public function handle()
    {
        $n = 0;
        Alumno::whereNull('qr_token')->chunkById(500, function($chunk) use (&$n) {
            foreach ($chunk as $alumno) {
                $alumno->qr_token = (string) Str::uuid();
                $alumno->save();
                $n++;
            }
        });
        $this->info("Tokens asignados: {$n}");
    }
}
