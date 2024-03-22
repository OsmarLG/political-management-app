<?php

namespace App\Livewire;

use App\Models\Zona;
use App\Models\Casilla;
use App\Models\Manzana;
use App\Models\Seccion;
use Livewire\Component;
use App\Models\Ejercicio;
use App\Models\EncuestaRespuesta;

class MapaGeneral extends Component
{
    public function render()
    {
        $zonas = 0; $secciones = 0; $manzanas = 0; $ejercicios = 0; $casillas = 0; $bardas = 0;
        
        if (auth()->user()->hasRole('MASTER') || auth()->user()->hasRole('ADMIN')) {
            $zonas = Zona::with('asignacionesGeograficas')->get()
                ->map(function ($zona) {
                    return [
                        'id' => $zona->id,
                        'nombre' => $zona->nombre,
                        'asignacionesGeograficas' => $zona->asignacionesGeograficas->map(function ($asignacion) {
                            return [
                                // Aquí estructuras los datos de asignación geográfica como los necesites
                                'latitud' => $asignacion->latitud,
                                'longitud' => $asignacion->longitud,
                                // ... otros campos de asignaciones geográficas
                            ];
                        }),
                    ];
                });   
            $secciones = Seccion::with('asignacionesGeograficas')->get()
                ->map(function ($seccion) {
                    return [
                        'id' => $seccion->id,
                        'nombre' => $seccion->nombre,
                        'asignacionesGeograficas' => $seccion->asignacionesGeograficas->map(function ($asignacion) {
                            return [
                                // Aquí estructuras los datos de asignación geográfica como los necesites
                                'latitud' => $asignacion->latitud,
                                'longitud' => $asignacion->longitud,
                                // ... otros campos de asignaciones geográficas
                            ];
                        })->toArray(),
                    ];
                });   
            $secciones = Seccion::with('asignacionesGeograficas')->get()
                ->map(function ($seccion) {
                    return [
                        'id' => $seccion->id,
                        'nombre' => $seccion->nombre,
                        'asignacionesGeograficas' => $seccion->asignacionesGeograficas->map(function ($asignacion) {
                            return [
                                // Aquí estructuras los datos de asignación geográfica como los necesites
                                'latitud' => $asignacion->latitud,
                                'longitud' => $asignacion->longitud,
                                // ... otros campos de asignaciones geográficas
                            ];
                        })->toArray(),
                    ];
                });   
            $manzanas = Manzana::with('asignacionesGeograficas')->get()
                ->map(function ($manzana) {
                    return [
                        'id' => $manzana->id,
                        'nombre' => $manzana->nombre,
                        'asignacionesGeograficas' => $manzana->asignacionesGeograficas->map(function ($asignacion) {
                            return [
                                // Aquí estructuras los datos de asignación geográfica como los necesites
                                'latitud' => $asignacion->latitud,
                                'longitud' => $asignacion->longitud,
                                // ... otros campos de asignaciones geográficas
                            ];
                        })->toArray(),
                    ];
                });   
            $ejercicios = Ejercicio::with('asignacionGeografica')->get()
                ->map(function ($ejercicio) {
                    $respuesta = EncuestaRespuesta::where('ejercicio_id', $ejercicio->id)->where('pregunta_id', 2)->get()->first()->respuesta;
                    
                    return [
                        'id' => $ejercicio->id,
                        'folio' => $ejercicio->folio,
                        'user' => [
                            'id' => $ejercicio->user ? $ejercicio->user->id : null,
                            'nombre' => $ejercicio->user ? $ejercicio->user->name . ' ' . $ejercicio->user->apellido_paterno . ' ' . $ejercicio->user->apellido_materno : null,
                        ],
                        'asignacionGeografica' => [
                            'latitud' => $ejercicio->asignacionGeografica ? $ejercicio->asignacionGeografica->latitud : null,
                            'longitud' => $ejercicio->asignacionGeografica ? $ejercicio->asignacionGeografica->longitud : null,
                        ],
                        'respuesta' => $respuesta,                    
                        'fecha' => $ejercicio->created_at->diffForHumans(),                    
                    ];
                });   
            $casillas = Casilla::with('asignacionGeografica')->get()
                ->map(function ($casilla) {
                    return [
                        'id' => $casilla->id,
                        'numero' => $casilla->numero,
                        'asignacionGeografica' => [
                            'latitud' => $casilla->asignacionGeografica ? $casilla->asignacionGeografica->latitud : null,
                            'longitud' => $casilla->asignacionGeografica ? $casilla->asignacionGeografica->longitud : null,
                        ],                  
                    ];
                });   
        }

        // dd($zonas, $secciones, $manzanas, $ejercicios);

        return view('livewire.mapa-general', [
            'zonas' => $zonas,
            'secciones' => $secciones,
            'manzanas' => $manzanas,
            'ejercicios' => $ejercicios,
            'casillas' => $casillas,
        ]);
    }
}
