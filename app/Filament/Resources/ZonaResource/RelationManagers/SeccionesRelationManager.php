<?php

namespace App\Filament\Resources\ZonaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Seccion;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SeccionesRelationManager extends RelationManager
{
    protected static string $relationship = 'secciones';
    protected static string $model = Seccion::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255)
                ->label('Nombre de la sección')
                ->placeholder('Introduce el nombre de la sección'),

                Forms\Components\Textarea::make('descripcion')
                ->maxLength(65535)
                ->label('Descripción')
                ->placeholder('Introduce una descripción para la sección'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Activa'),

                // Agregar aquí más columnas si es necesario
            ])
            ->filters([
                // Definir filtros si se requieren
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
