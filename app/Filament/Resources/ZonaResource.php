<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Zona;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ZonaResource\Pages;
use Filament\Tables\Columns\Layout\View as ViewsT;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ZonaResource\RelationManagers;

class ZonaResource extends Resource
{
    protected static ?string $model = Zona::class;

    protected static ?string $label = 'Zona';
    protected static ?string $pluralLabel = 'Zonas';
    protected static ?string $navigationLabel = 'Zonas';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationIcon = 'heroicon-s-globe-alt';    
    protected static ?int    $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
                ->schema([
                    Section::make('Detalles de la Zona')
                        ->schema([
                            TextInput::make('nombre')
                                ->required()
                                ->maxLength(255)
                                ->label('Nombre')
                                ->placeholder('Escriba el nombre de la zona')
                                ->helperText('El nombre único para la zona.')
                                ->columnSpan([
                                    'sm' => 2,
                                ]),
    
                            Textarea::make('descripcion')
                                ->maxLength(65535)
                                ->label('Descripción')
                                ->placeholder('Escriba una descripción detallada')
                                ->helperText('Proporcione una descripción detallada de la zona.')
                                ->columnSpan([
                                    'sm' => 2,
                                ]),
                        ])
                        ->columns([
                            'sm' => 2,
                        ]),
                    
                    Section::make('Estado y Configuración')
                        ->schema([
                            Select::make('status')
                                ->options([
                                    'ACTIVO' => 'Activo',
                                    'INACTIVO' => 'Inactivo',
                                ])
                                ->default('ACTIVO')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->label('Estado')
                                ->placeholder('Seleccione el estado de la zona')
                                ->helperText('El estado determina si la zona está activa para su uso.'),
                        ]),
                    
                    Section::make('Ubicación Geográfica')
                        ->schema([                                        
                            View::make('zonas.map'),
                        ]),
                ])
                ->columnSpan([
                    'sm' => 2,
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre'),
                TextColumn::make('descripcion')->limit(50),

                // Columna para el número de secciones
                TextColumn::make('secciones_count')
                ->label('Número de Secciones')
                ->counts('secciones'),

                // Columna para el número de manzanas
                TextColumn::make('manzanas_count')
                    ->label('Número de Manzanas')
                    ->counts('manzanas'),
                    
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\SeccionesRelationManager::class,
            RelationManagers\ManzanasRelationManager::class,
            RelationManagers\AsignacionesGeograficasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListZonas::route('/'),
            'create' => Pages\CreateZona::route('/create'),
            'edit' => Pages\EditZona::route('/{record}/edit'),
        ];
    }

    public static function getModel(): string
    {
        return \App\Models\Zona::class;
    }
}
