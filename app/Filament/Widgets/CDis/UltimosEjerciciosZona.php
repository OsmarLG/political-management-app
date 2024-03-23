<?php

namespace App\Filament\Widgets\CDis;

use App\Models\Zona;
use Filament\Tables;
use App\Models\Ejercicio;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosEjerciciosZona extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $zona = Zona::find($zonaId = auth()->user()->Asignacion->id_modelo);
        $manzanas = $zona->manzanas;
        $idManzanas = $manzanas->pluck('id');

        return $table
            ->query(Ejercicio::query()->whereIn('manzana_id', $idManzanas)->latest('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('folio')->label('Folio'),                
                Tables\Columns\TextColumn::make('user.fullname')->label('Usuario'),                
                Tables\Columns\TextColumn::make('manzana.nombre')->label('Manzana'),                
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de Creación')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['C DISTRITAL']);
    }
}
