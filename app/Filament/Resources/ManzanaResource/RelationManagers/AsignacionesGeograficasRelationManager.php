<?php

namespace App\Filament\Resources\ManzanaResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\View;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class AsignacionesGeograficasRelationManager extends RelationManager
{
    protected static string $relationship = 'asignacionesGeograficas';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('descripcion')
                ->required()
                ->maxLength(255)
                ->columnSpan([
                    'sm' => 2,
                ]),
    
            TextInput::make('longitud')
                ->numeric()
                ->label('Longitud')
                ->required(),
    
            TextInput::make('latitud')
                ->numeric()
                ->label('Latitud')
                ->required(),
    
            View::make('components.map')
                ->columnSpan([
                    'sm' => 2,
                ]),
        ])
        ->columns([
            'sm' => 2,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                Tables\Columns\TextColumn::make('descripcion'),
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
