<?php

namespace App\Filament\Resources\EjercicioResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EjercicioResource;
use App\Models\Manzana;
use App\Models\Seccion;
use App\Models\UsuarioAsignacion;

class ListEjercicios extends ListRecords
{
    protected static string $resource = EjercicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {

        if(auth()->user()->hasRole(['C DISTRITAL'])){
            $consulta_aginacion = UsuarioAsignacion::where('modelo','Zona')->where('user_id',auth()->user()->id)->first();
            $zona_id = $consulta_aginacion ? Seccion::find($consulta_aginacion->id_modelo)->id: null ;
            
            return [
                    'Todas' => Tab::make()->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereHas('manzana', function ($query) use ($zona_id) {
                        $query->whereHas('seccion', function ($query) use ($zona_id) {
                            $query->where('zona_id', $zona_id);
                        });
                    })
                ),
            ];
        }

        if(auth()->user()->hasRole(['C ENLACE DE MANZANA'])){
            $consulta_aginacion = UsuarioAsignacion::where('modelo','Seccion')->where('user_id',auth()->user()->id)->first();
            $seccion_id = $consulta_aginacion ? Seccion::find($consulta_aginacion->id_modelo)->id: null ;
            
            return [
                    'Todas' => Tab::make()->modifyQueryUsing(fn (Builder $query) => 
                    $query->whereHas('manzana', function ($query) use ($seccion_id) {
                        $query->where('seccion_id', $seccion_id);
                    })
                ),
            ];
        }

        if(auth()->user()->hasRole(['MANZANAL'])){
            $consulta_aginacion = UsuarioAsignacion::where('modelo','Manzana')->where('user_id',auth()->user()->id)->first();
            $manzana_id = $consulta_aginacion ? Manzana::find($consulta_aginacion->id_modelo)->id: null ;
            
            return [
                    'Todas' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('user_id', auth()->user()->id)
                ),
            ];
        }

        if(auth()->user()->hasRole(['MASTER', 'ADMIN'])){
            return [
                'Todas' => Tab::make()->modifyQueryUsing(fn (Builder $query) => $query
            ),
        
            ];
        }

    }
 
    public function getDefaultActiveTab(): string | int | null
    {
        return 'Todas';
    }


}
