<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\UsuarioAsignacion;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Models\{User, Zona, Seccion, Manzana};
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UsuarioAsignacionResource\Pages;
use App\Filament\Resources\UsuarioAsignacionResource\RelationManagers;

class UsuarioAsignacionResource extends Resource
{
    protected static ?string $model = UsuarioAsignacion::class;

    protected static ?string $label = 'Asignacion';
    protected static ?string $pluralLabel = 'Asignaciones';
    protected static ?string $navigationLabel = 'Asignaciones';
    protected static ?string $navigationGroup = 'Users Settings';
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';    
    protected static ?string $slug = 'usuarios_asignaciones';    
    protected static ?int    $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('user_id')
                ->label('Usuario')
                ->relationship('user', 'name')
                ->options(function () { 
                        $query = User::query();
                            $query                        
                            ->whereDoesntHave('Asignacion')
                            ->with('roles')
                            ->active()
                            ->whereDoesntHave('roles', function ($query) {
                                $query->whereIn('name', ['MASTER', 'ADMIN']);
                            });
                            if(auth()->user()->hasRole('SECCIONAL')){
                                $query->whereHas('roles', function ($query) {
                                    $query->where('name', 'MANZANAL');
                                });
                            }
                            if(auth()->user()->hasRole('ZONAL')){
                                $query->whereHas('roles', function ($query) {
                                    $query->where('name', 'MANZANAL');
                                    $query->orWhere('name', 'SECCIONAL');
                                });
                            }

                            return $query->get()
                            ->pluck('full_name', 'id');
                        }
                )
                ->reactive()
                ->afterStateUpdated(function ($state, $component, $set) {
                    $user = User::find($state);
                    $roleName = $user->roles->first()?->name; // Obtén el nombre del primer rol
                    // Aquí asumimos que los nombres de los roles y los modelos están en minúsculas y son idénticos
                    $set('modelo', $roleName === 'ZONAL' ? 'Zona' : ($roleName === 'SECCIONAL' ? 'Seccion' : 'Manzana'));
                })
                ->searchable()                  
                ->preload()
                ->live(),

            // El campo 'modelo' se rellenará automáticamente basándose en el rol del usuario seleccionado
            Forms\Components\TextInput::make('modelo')//TIPO DE ASIGANCION 
                ->label('Tipo de Asignación')
                ->readOnly(),

            Forms\Components\Select::make('id_modelo')
            ->label('Asignación')
            ->options(function (callable $get) {
                $modelo = $get('modelo');
                $user = auth()->user();
                
                // Encuentra la zona asignada al usuario ZONAL
                $userZoneId = optional($user->Asignacion()->where('modelo', 'Zona')->first())->id_modelo;
        
                // Encuentra las IDs ya asignadas para filtrarlas de las opciones
                $assignedIds = UsuarioAsignacion::where('modelo', $modelo)->pluck('id_modelo')->toArray();
        
                switch ($modelo) {
                    case 'Zona':
                        // Retorna las zonas que no han sido asignadas aún
                        return Zona::when($user->hasRole(['MASTER', 'ADMIN']), function ($query) use ($assignedIds) {
                                return $query->whereNotIn('id', $assignedIds);
                            })->pluck('nombre', 'id');
        
                    case 'Seccion':
                        // Retorna las secciones de la zona del usuario que no han sido asignadas aún
                        return Seccion::when($user->hasRole(['MASTER', 'ADMIN', 'ZONAL']) && $userZoneId, function ($query) use ($userZoneId, $assignedIds) {
                                return $query->where('zona_id', $userZoneId)
                                             ->whereNotIn('id', $assignedIds);
                            })->pluck('nombre', 'id');
        
                    case 'Manzana':
                        if(auth()->user()->hasRole(['MASTER','ADMIN'])){
                            return Manzana::all()->pluck('nombre', 'id');
                        }
                        if(auth()->user()->hasRole(['ZONAL','SECCIONAL'])){
                            return Manzana::where('seccion_id',function($query){
                                $query->select(DB::raw('id_modelo FROM users,usuario_asignaciones WHERE users.id = usuario_asignaciones.user_id
                                AND users.id = '.auth()->user()->id));
                            })
                            ->pluck('nombre', 'id');
                        }
                        // Retorna las manzanas de las secciones de la zona del usuario que no han sido asignadas aún
                        return Manzana::when($user->hasRole(['MASTER', 'ADMIN', 'ZONAL', 'SECCIONAL']) && $userZoneId, function ($query) use ($userZoneId, $assignedIds) {
                                return $query->whereHas('seccion', function ($subQuery) use ($userZoneId) {
                                        $subQuery->where('zona_id', $userZoneId);
                                    })
                                    ->whereNotIn('id', $assignedIds);
                            })->pluck('nombre', 'id');
        
                    default:
                        return [];
                }
            }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
                TextColumn::make('user.name')
                ->label('Nombre del Usuario')
                ->sortable()
                ->searchable(),
                
                TextColumn::make('user.roles.name')
                    ->label('Rol')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('asignable.nombre')
                    ->label('Asignación')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state, $record) {
                        return optional($record->asignable)->nombre;
                    }),
                
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d-m-Y')
                    ->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsuarioAsignacions::route('/'),
            'create' => Pages\CreateUsuarioAsignacion::route('/create'),
            'edit' => Pages\EditUsuarioAsignacion::route('/{record}/edit'),
        ];
    }
}
