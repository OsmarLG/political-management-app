<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Relations\Relation;

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
    public function boot(): void
    {
        //
        Relation::morphMap([
            'Zona' => 'App\Models\Zona',
            'Seccion' => 'App\Models\Seccion',
            'Manzana' => 'App\Models\Manzana',
            'Ejercicio' => 'App\Models\Ejercicio',
            'Encuesta' => 'App\Models\Encuesta',
            'Barda' => 'App\Models\Barda',
            'Casilla' => 'App\Models\Casilla',
        ]);

        FilamentAsset::register([
            Js::make('leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js'),
            Css::make('leaflet-css', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'),
            Js::make('appjs', __DIR__ . '/../../resources/js/app.js')->loadedOnRequest(),
            Css::make('appcss', __DIR__ . '/../../resources/css/app.css')->loadedOnRequest(),
        ]);
    }
}
