<?php

namespace App\Filament\Widgets\MasAdm;

use App\Models\Seccion;
use App\Models\Zona;
use Filament\Widgets\ChartWidget;

class IntecionPorSeccion extends ChartWidget
{
    protected static ?string $heading = 'Inteción de voto por Sección';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        
        if($activeFilter == null || $activeFilter == 0){
            $secciones = Seccion::all();
        }else{
            $secciones = Seccion::where("zona_id",$activeFilter)->get();
        }
         // Obtiene todos los secciones

        $labels = $secciones->pluck('nombre')->all(); // Extrae los usernames como etiquetas

        $data = $secciones->map(function ($seccion) {
            return $seccion->IntencionVoto;
        })->all();
        $colors = ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF'];
        return [
            'datasets' => [
                [
                    'label' => 'Ejercicios por Usuario',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $labels,
        ];
      
    }

    protected function getType(): string
    {
        return 'pie';
    }
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['MASTER', 'ADMIN']);
    }

    protected function getFilters(): ?array
    {
        $zonas = Zona::all();

        $zonas_filtros = array();

        $zonas_filtros[0] = "Todas";
        foreach($zonas as $zona){
            $zonas_filtros[$zona->id] = $zona->nombre;
        }

        return  $zonas_filtros;
    }



}
