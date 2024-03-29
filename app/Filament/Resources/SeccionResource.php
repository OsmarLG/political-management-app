<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Seccion;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\UsuarioAsignacion;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SeccionResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SeccionResource\RelationManagers;

class SeccionResource extends Resource
{
    protected static ?string $model = Seccion::class;

    protected static ?string $label = 'Seccion';
    protected static ?string $pluralLabel = 'Secciones';
    protected static ?string $navigationLabel = 'Secciones';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationIcon = 'heroicon-m-globe-europe-africa';    
    protected static ?int    $navigationSort = 2;
    
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
                ->schema([
                    Section::make('Detalles de la Sección')
                        ->schema([
                            TextInput::make('nombre')
                                ->required()
                                ->maxLength(255)
                                ->label('Nombre de la sección')
                                ->placeholder('Escribe el nombre de la sección')
                                ->columnSpan(2),

                            Textarea::make('descripcion')
                                ->maxLength(65535)
                                ->label('Descripción de la sección')
                                ->placeholder('Proporciona una descripción detallada')
                                ->columnSpan(2),

                            Select::make('zona_id')
                                ->relationship('zona', 'nombre')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->label('Zona')
                                ->placeholder('Selecciona una zona')
                                ->columnSpan(2),

                            Section::make('Ubicación Geográfica')
                                ->schema([                                        
                                    View::make('secciones.map'),
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
                        ])
                        ->columns(2)
                        ->collapsible(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('descripcion'),
                Tables\Columns\TextColumn::make('zona.nombre')->searchable(),
                // Columna para el número de manzanas
                Tables\Columns\TextColumn::make('manzanas_count')
                ->label('Número de Manzanas')
                ->counts('manzanas'),
                Tables\Columns\TextColumn::make('intencionVoto')->searchable(),
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
            RelationManagers\ManzanasRelationManager::class,
            RelationManagers\AsignacionesGeograficasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeccions::route('/'),
            'create' => Pages\CreateSeccion::route('/create'),
            'edit' => Pages\EditSeccion::route('/{record}/edit'),
        ];
    }

    public static function getModel(): string
    {
        return \App\Models\Seccion::class;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('MASTER') || auth()->user()->hasRole('ADMIN')) {
            return $query;
        } else if (auth()->user()->hasRole('C DISTRITAL')) {
            $zonaId = UsuarioAsignacion::where('modelo', 'Zona')->where('user_id', auth()->user()->id)->get()->first()->id_modelo;
            return $query->where('zona_id', $zonaId);
        }

        // O cualquier lógica adicional que necesites
        return $query;
    }
}
