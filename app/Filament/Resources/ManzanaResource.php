<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManzanaResource\Pages;
use App\Filament\Resources\ManzanaResource\RelationManagers;
use App\Models\Manzana;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManzanaResource extends Resource
{
    protected static ?string $model = Manzana::class;

    protected static ?string $label = 'Manzana';
    protected static ?string $pluralLabel = 'Manzanas';
    protected static ?string $navigationLabel = 'Manzanas';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationIcon = 'heroicon-s-home-modern';    
    protected static ?int    $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion'),
                Forms\Components\Select::make('seccion_id')
                    ->relationship('seccion', 'nombre')
                    ->preload()
                    ->searchable()
                    ->live()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('seccion.nombre')->searchable(),
                Tables\Columns\TextColumn::make('descripcion'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManzanas::route('/'),
            'create' => Pages\CreateManzana::route('/create'),
            'edit' => Pages\EditManzana::route('/{record}/edit'),
        ];
    }

    public static function getModel(): string
    {
        return \App\Models\Manzana::class;
    }
}
