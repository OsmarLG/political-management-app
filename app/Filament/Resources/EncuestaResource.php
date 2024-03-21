<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Encuesta;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EncuestaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EncuestaResource\RelationManagers;

class EncuestaResource extends Resource
{
    protected static ?string $model = Encuesta::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?int    $navigationSort = 5;
    protected static ?string $navigationLabel = 'Encuesta';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make()
                ->schema([
                    Section::make('Detalles de la encuesta')
                    ->schema([
                        TextInput::make('titulo')
                        ->required()
                        ->maxLength(255)
                        ->label('Titulo')
                        ->placeholder('Escriba el titulo de la encuesta')
                        ->helperText('El nombre único para la encuesta.')
                        ->columnSpan([
                            'sm' => 2,
                        ]),
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('titulo')
                ->label('Titulo')
                ->sortable()
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PreguntasRelationManager::class,
            RelationManagers\OpcionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEncuestas::route('/'),
            'create' => Pages\CreateEncuesta::route('/create'),
            'edit' => Pages\EditEncuesta::route('/{record}/edit'),
        ];
    }
}
