<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Grado;
use App\Models\Seccion;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        view()->share('gradosGlobal', Grado::all());
        view()->share('seccionesGlobal', Seccion::all());
    }

}
