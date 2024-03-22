<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Zona;
use Filament\Tables;
use App\Models\Barda;
use App\Models\Seccion;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\UsuarioAsignacion;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BardaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BardaResource\RelationManagers;

class BardaResource extends Resource
{
    protected static ?string $model = Barda::class;
    protected static ?string $label = 'Barda';
    protected static ?string $pluralLabel = 'Bardas';
    protected static ?string $navigationLabel = 'Bardas';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';    
    protected static ?int    $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
                ->schema([
                    Section::make('Detalles de la Barda')
                        ->schema([
                            TextInput::make('identificador')->placeholder('Identificador de Barda')->required()->maxLength(255),
                            
                            // Select para zona que actualiza las secciones disponibles.
                            Select::make('zona_id')
                                ->options(function(){
                                    return Zona::all()->pluck('nombre', 'id');
                                })
                                ->searchable()
                                ->reactive()
                                ->live()
                                ->preload()
                                ->label('Zona')
                                ->placeholder('Selecciona una Zona')
                                ->hidden(auth()->user()->hasRole('C DISTRITAL')) // Oculta el campo si el usuario es 'C DISTRITAL'
                                ->afterStateUpdated(fn (callable $set) => $set('seccion_id', null)),
                        
                            Select::make('seccion_id')
                                ->label('Sección')
                                ->options(function (callable $get) {
                                    if (auth()->user()->hasRole('C DISTRITAL')) {
                                        // Aquí asumimos que tienes una relación o propiedad que determina la zona del usuario
                                        $zonaId = UsuarioAsignacion::where('modelo', 'Zona')->where('user_id', auth()->user()->id)->get()->first()->id_modelo;
                                        return Seccion::where('zona_id', $zonaId)->pluck('nombre', 'id');
                                    } else {
                                        $zonaId = $get('zona_id');
                                        return Seccion::when($zonaId, function ($query) use ($zonaId) {
                                            return $query->where('zona_id', $zonaId);
                                        })->pluck('nombre', 'id');
                                    }
                                })
                                // ...
                                ->required()
                                ->reactive(),

                            Section::make('Coordenadas de Ubicación Geográfica')
                                ->schema([                                        
                                    View::make('bardas.map'),
                                    TextInput::make('Latitud')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Latitud')
                                    ->placeholder('Latitud')
                                    ->helperText('Coordenadas para el ejercicio.')
                                    ->readOnly(),

                                    TextInput::make('Longitud')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Longitud')
                                    ->placeholder('Longitud')
                                    ->helperText('Coordenadas para el ejercicio.')
                                    ->readOnly(),
                                ]),
                        ])
                        ->columns(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('identificador')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seccion.nombre')->searchable(),
                Tables\Columns\TextColumn::make('asignacionGeografica.latitud')->label('Latitud'),
                Tables\Columns\TextColumn::make('asignacionGeografica.longitud')->label('Longitud'),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('MASTER') || auth()->user()->hasRole('ADMIN')) {
            // El usuario master o admin pueden ver todas las bardas
            return $query;
        } else if (auth()->user()->hasRole('C DISTRITAL')) {
            // El usuario C DISTRITAL sólo puede ver las bardas asignadas a las secciones a su cargo
            // Suponiendo que tienes un método en tu modelo User que devuelve las secciones correspondientes
            $zonaId = UsuarioAsignacion::where('modelo', 'Zona')->where('user_id', auth()->user()->id)->get()->first()->id_modelo;
            $zona = Zona::find($zonaId);
            $seccionesIds = $zona->secciones->pluck('id');
            return $query->whereIn('seccion_id', $seccionesIds);
        }

        // O cualquier lógica adicional que necesites
        return $query;
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
            'index' => Pages\ListBardas::route('/'),
            'create' => Pages\CreateBarda::route('/create'),
            'edit' => Pages\EditBarda::route('/{record}/edit'),
        ];
    }
}
