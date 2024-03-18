<?php

namespace App\Filament\Resources\EncuestaResource\RelationManagers;

use App\Models\EncuestaPregunta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OpcionesRelationManager extends RelationManager
{
    protected static string $relationship = 'opciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pregunta_id')
                    ->label('Pregunta')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->options(function (callable $get) {
                        // Aquí asumimos que estás dentro del RelationManager de Zona y 
                        // que puedes acceder al registro de zona actual con $this->ownerRecord
                        $encuesta_id = $this->ownerRecord->id;
                        
                        return EncuestaPregunta::where('encuesta_id', $encuesta_id)
                                      ->pluck('texto_pregunta', 'id');
                    })
                    ->required(),
                Forms\Components\TextInput::make('texto_opcion')
                    ->label('Opcion')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('texto_opcion')
            ->columns([
                Tables\Columns\TextColumn::make('texto_opcion')
                ->searchable()
                ->label('Opcion'),
                Tables\Columns\TextColumn::make('pregunta.texto_pregunta')
                ->searchable()
                ->label('Pregunta'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Crear Opcion'),
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
