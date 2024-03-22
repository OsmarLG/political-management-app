<?php

namespace App\Filament\Widgets\MasAdm;

use Filament\Tables;
use App\Models\Ejercicio;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UltimosEjercicios extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';


    public function table(Table $table): Table
    {
        return $table
            ->query(Ejercicio::query()->latest('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID'),
                Tables\Columns\TextColumn::make('folio')->label('Folio'),                
                Tables\Columns\TextColumn::make('user.fullname')->label('Usuario'),                
                Tables\Columns\TextColumn::make('manzana.nombre')->label('Manzana'),                
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de Creación')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc'); // Esto asegura que el ordenamiento por defecto sea el deseado
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['MASTER', 'ADMIN']);
    }
}
