<?php

namespace App\Livewire;

use App\Models\Zona;
use App\Models\Barda;
use App\Models\Casilla;
use App\Models\Manzana;
use App\Models\Seccion;
use Livewire\Component;
use App\Models\Ejercicio;
use App\Models\EncuestaRespuesta;
use App\Models\UsuarioAsignacion;

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
                        'zona' => $seccion->zona,
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
                    $lona = EncuestaRespuesta::where('ejercicio_id', $ejercicio->id)->where('pregunta_id', 3)->get()->first()->respuesta;
                    
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
                        'lona' => $lona,                    
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
            $bardas = Barda::with('asignacionGeografica')->get()
                ->map(function ($barda) {
                    return [
                        'id' => $barda->id,
                        'identificador' => $barda->identificador,
                        'seccion' => $barda->seccion,
                        'asignacionGeografica' => [
                            'latitud' => $barda->asignacionGeografica ? $barda->asignacionGeografica->latitud : null,
                            'longitud' => $barda->asignacionGeografica ? $barda->asignacionGeografica->longitud : null,
                        ],                  
                    ];
                });   
        } else if (auth()->user()->hasRole('C DISTRITAL')) {

            $zona = null;
            try {
                $zonaId = UsuarioAsignacion::where('modelo', 'Zona')->where('user_id', auth()->user()->id)->get()->first()->id_modelo;
                $zona = Zona::find($zonaId);
            } catch (\Throwable $th) {

            }

            if ($zona != null) {
                $zonas = collect([$zona])->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nombre' => $item->nombre,
                        'asignacionesGeograficas' => $item->asignacionesGeograficas->map(function ($asignacion) {
                            return [
                                'latitud' => $asignacion->latitud,
                                'longitud' => $asignacion->longitud,
                                // ... otros campos de asignaciones geográficas
                            ];
                        }),
                    ];
                });

                $secciones = Seccion::where('zona_id', $zona->id)->with('asignacionesGeograficas')->get()
                ->map(function ($seccion) {
                    return [
                        'id' => $seccion->id,
                        'nombre' => $seccion->nombre,
                        'zona' => $seccion->zona,
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
                
                $idsSecciones = $secciones->pluck('id');
                
                $manzanas = Manzana::whereIn('seccion_id', $idsSecciones)->get()
                ->map(function ($manzana) {
                    return [
                        'id' => $manzana->id,
                        'nombre' => $manzana->nombre,
                        'seccion' => $manzana->seccion,
                        'zona' => $manzana->zona,
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
                
                $idsManzanas = $manzanas->pluck('id');

                $bardas = Barda::whereIn('seccion_id', $idsSecciones)->get()
                ->map(function ($barda) {
                    return [
                        'id' => $barda->id,
                        'identificador' => $barda->identificador,
                        'seccion' => $barda->seccion,
                        'asignacionGeografica' => [
                            'latitud' => $barda->asignacionGeografica ? $barda->asignacionGeografica->latitud : null,
                            'longitud' => $barda->asignacionGeografica ? $barda->asignacionGeografica->longitud : null,
                        ],                  
                    ];
                }); 

                $ejercicios = Ejercicio::whereIn('manzana_id', $idsManzanas)->get()
                ->map(function ($ejercicio) {
                    $respuesta = EncuestaRespuesta::where('ejercicio_id', $ejercicio->id)->where('pregunta_id', 2)->get()->first()->respuesta ?? '';
                    $lona = EncuestaRespuesta::where('ejercicio_id', $ejercicio->id)->where('pregunta_id', 3)->get()->first()->respuesta ?? '';
                    
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
                        'lona' => $lona,                    
                        'fecha' => $ejercicio->created_at->diffForHumans(),                    
                    ];
                });
            }
        } else if (auth()->user()->hasRole('C ENLACE DE MANZANA')) {

            $seccion = null;
            try {
                $seccionId = UsuarioAsignacion::where('modelo', 'Seccion')->where('user_id', auth()->user()->id)->get()->first()->id_modelo;
                $seccion = Seccion::find($seccionId);
            } catch (\Throwable $th) {

            }

            if ($seccion != null) { 
                $secciones = collect([$seccion])->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nombre' => $item->nombre,
                        'zona' => $item->zona,
                        'asignacionesGeograficas' => $item->asignacionesGeograficas->map(function ($asignacion) {
                            return [
                                'latitud' => $asignacion->latitud,
                                'longitud' => $asignacion->longitud,
                                // ... otros campos de asignaciones geográficas
                            ];
                        }),
                    ];
                });
            }

            $idsSecciones = $secciones->pluck('id');
                
            $manzanas = Manzana::whereIn('seccion_id', $idsSecciones)->get()
            ->map(function ($manzana) {
                return [
                    'id' => $manzana->id,
                    'nombre' => $manzana->nombre,
                    'seccion' => $manzana->seccion,
                    'zona' => $manzana->zona,
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
            
            $idsManzanas = $manzanas->pluck('id');

            $ejercicios = Ejercicio::whereIn('manzana_id', $idsManzanas)->get()
            ->map(function ($ejercicio) {
                $respuesta = EncuestaRespuesta::where('ejercicio_id', $ejercicio->id)->where('pregunta_id', 2)->get()->first()->respuesta;
                $lona = EncuestaRespuesta::where('ejercicio_id', $ejercicio->id)->where('pregunta_id', 3)->get()->first()->respuesta;
                
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
                    'lona' => $lona,                    
                    'fecha' => $ejercicio->created_at->diffForHumans(),                    
                ];
            });
        } else if (auth()->user()->hasRole('MANZANAL')) {
            $manzana = null;
            try {
                $manzanaId = UsuarioAsignacion::where('modelo', 'Manzana')->where('user_id', auth()->user()->id)->get()->first()->id_modelo;
                $manzana = Manzana::find($manzanaId);
            } catch (\Throwable $th) {

            }

            if ($manzana != null) { 
                $manzanas = collect([$manzana])->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nombre' => $item->nombre,
                        'zona' => $item->zona,
                        'seccion' => $item->seccion,
                        'asignacionesGeograficas' => $item->asignacionesGeograficas->map(function ($asignacion) {
                            return [
                                'latitud' => $asignacion->latitud,
                                'longitud' => $asignacion->longitud,
                                // ... otros campos de asignaciones geográficas
                            ];
                        }),
                    ];
                });
            }

            $ejercicios = Ejercicio::where('user_id', auth()->user()->id)->get()
            ->map(function ($ejercicio) {
                $respuesta = EncuestaRespuesta::where('ejercicio_id', $ejercicio->id)->where('pregunta_id', 2)->get()->first()->respuesta;
                $lona = EncuestaRespuesta::where('ejercicio_id', $ejercicio->id)->where('pregunta_id', 3)->get()->first()->respuesta;
                
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
                    'lona' => $lona,                    
                    'fecha' => $ejercicio->created_at->diffForHumans(),                    
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
            'bardas' => $bardas,
        ]);
    }
}
