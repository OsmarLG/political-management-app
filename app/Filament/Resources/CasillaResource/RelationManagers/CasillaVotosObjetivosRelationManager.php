<?php

namespace App\Filament\Resources\CasillaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CasillaVotosObjetivosRelationManager extends RelationManager
{
    protected static string $relationship = 'CasillaVotosObjetivos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero_votos_objetivos')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ano')
                    ->required()
                    ->maxLength(4) // Ajustamos la longitud máxima a 4 para años.
                    ->rule('digits:4') // Aseguramos que tenga 4 dígitos.
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_votos_objetivos')
            ->columns([
                Tables\Columns\TextColumn::make('numero_votos_objetivos')->label('Numero Objetivo de Votos'),
                Tables\Columns\TextColumn::make('ano')->label('Año'),
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
