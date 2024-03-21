<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Zona;
use Filament\Tables;
use App\Models\Manzana;
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
use App\Filament\Resources\ManzanaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ManzanaResource\RelationManagers;

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
                Section::make()
                    ->schema([
                        Section::make('Detalles de la Manzana')
                            ->schema([
                                TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Nombre de la manzana')
                                    ->placeholder('Escribe el nombre de la manzana')
                                    ->columnSpan(2),

                                Textarea::make('descripcion')
                                    ->maxLength(65535)
                                    ->label('Descripción de la manzana')
                                    ->placeholder('Proporciona una descripción detallada')
                                    ->columnSpan(2),

                                Select::make('zona_id')
                                    ->label('Zona')
                                    ->options(Zona::all()->pluck('nombre', 'id'))
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set) => $set('seccion_id', null)),
                                Select::make('seccion_id')
                                    ->label('Sección')
                                    ->options(function (callable $get) {
                                        $zonaId = $get('zona_id');
                                        // Si no se ha seleccionado una zona, devuelve todas las secciones.
                                        // De lo contrario, filtra las secciones por la zona seleccionada.
                                        return Seccion::when($zonaId, function ($query) use ($zonaId) {
                                            return $query->where('zona_id', $zonaId);
                                        })->pluck('nombre', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive() // Importante para asegurar que se actualiza cuando cambia zona_id
                                    ->required()
                                    ->label('Sección')
                                    ->placeholder('Selecciona una sección')
                                    ->columnSpan(2), 
                                Section::make('Ubicación Geográfica')
                                    ->schema([                                        
                                        View::make('manzanas.map'),
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
                Tables\Columns\TextColumn::make('seccion.nombre')->searchable(),
                Tables\Columns\TextColumn::make('seccion.zona.nombre')->searchable(),
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
            RelationManagers\AsignacionesGeograficasRelationManager::class,
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('MASTER') || auth()->user()->hasRole('ADMIN')) {
            return $query;
        } else if (auth()->user()->hasRole('C DISTRITAL')) {
            $zonaId = UsuarioAsignacion::where('modelo', 'Zona')->where('user_id', auth()->user()->id)->get()->first()->id_modelo;
            $zona = Zona::find($zonaId);
            $seccionesIds = $zona->secciones->pluck('id');
            return $query->whereIn('seccion_id', $seccionesIds);
        } else if (auth()->user()->hasRole('C ENLACE DE MANZANA')) {
            $seccionId = UsuarioAsignacion::where('modelo', 'Seccion')->where('user_id', auth()->user()->id)->get()->first()->id_modelo;
            return $query->where('seccion_id', $seccionId);
        }

        // O cualquier lógica adicional que necesites
        return $query;
    }
}
