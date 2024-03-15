<?php

namespace App\Filament\Resources\ZonaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Manzana;
use App\Models\Seccion;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ManzanasRelationManager extends RelationManager
{
    protected static string $relationship = 'manzanas';
    protected static string $model = Manzana::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255)
                ->label('Nombre de la manzana')
                ->placeholder('Introduce el nombre de la manzana'),

                Forms\Components\Textarea::make('descripcion')
                    ->maxLength(65535)
                    ->label('Descripción')
                    ->placeholder('Introduce una descripción para la manzana'),

                Forms\Components\Select::make('seccion_id')
                    ->label('Sección')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->options(function (callable $get) {
                        // Aquí asumimos que estás dentro del RelationManager de Zona y 
                        // que puedes acceder al registro de zona actual con $this->ownerRecord
                        $zonaId = $this->ownerRecord->id;
                        
                        return Seccion::where('zona_id', $zonaId)
                                      ->pluck('nombre', 'id');
                    })
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
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
                    ->label('Status'),

                Tables\Columns\TextColumn::make('seccion.nombre')
                    ->label('Sección')
                    ->searchable()
                    ->sortable(),
                ])
            ->filters([
                //
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
